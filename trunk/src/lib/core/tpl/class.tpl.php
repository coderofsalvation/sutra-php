<?
/** 
 * File:        <#file#>.php
 * Date:        Mon Sep 19 16:56:30 2011
 *
 * template wrapper class
 * 
 * Changelog:
 *
 * 	[Mon Sep 19 16:56:30 2011] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @version $id$
 * @copyright 2011 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
 * @license
 * %license%
 */

include_once( dirname(__FILE__)."/class.template.php" );

class tpl extends Template_Lite{
  
  private $_data;
  private $_inlines;


  public function __construct(){ 
    $this->resetInc();
    // register xtra functions
    $this->register_function( "inc",    array(&$this, "_inc" ) );
    $this->register_block( "inline", array(&$this, "_inline" ) );
    $this->register_function( "call",   array(&$this, "_call" ) );
    $this->register_outputfilter( array( &$this, "correctUrls" ) );
  }
    
  private function assignDefaults(){  
    $this->assign( "_GET", $_GET );
    $this->assign( "_POST", $_POST );
    $this->assign( "_SERVER", $_SERVER );
    $this->assign( "_SESSION", $_SESSION );
  }

  public function resetInc(){
    $this->_data            = array( "php" => array(), "js" => array(), "css" => array() );
    $this->_inlines         = array( "css" => array(), "js" => array(), "jsDomReady" => array() );
  }

  /*
   * _inc        - includes external files like CSS/Javascript 
   * 
   * @param mixed $params 
   * @access public
   * @return void
   */
  public    function inc( $file, $add = true ){ $this->_inc( array('file' => $file ), $add ); }
  protected function _inc( $params, $add ){
    if( isset( $params['file'] ) ){
      $parts    = pathinfo( $params['file'] );
      $ext      = $parts['extension'];
      _assert( isset( $this->_data[ $ext ] ), __FILE__.__METHOD__." '{$params['file']}' has unknown filetype!" );
      if( $add ){
        $this->_data[ $ext ][ $params['file'] ] = $params['file'];
      }else{
        foreach( $this->_data[ $ext ] as $key => $file )
          if( $file == $params['file'] )
            unset( $this->_data[ $ext ][ $key ] );
      } 
    }
  }


 /*
  * _inline        - inlineludes external files like CSS/Javascript
  *
  * @param mixed $params
  * @access public
  * @return void
  */
  public    function inline( $language, $code, $add = true ){ $this->_inline( array('language'=>$language), $code, $add ); }
  protected function _inline( $params, $code, $add ){
    if( isset( $params['language'] ) && strlen($code) ){
      $language = $params['language'];
      _assert( isset( $this->_inlines[ $language ] ), __FILE__.__METHOD__." '{$language}' has unknown filetype!" );
      if( $add ){
        $this->_inlines[ $language ][] = "\t".$code."\n";
      }else{
        foreach( $this->_inlines[ $language ] as $key => $value )
          if( $code == $value )
            unset( $this->_inlines[ $language ][ $key ] );
      }
    }
  }


  /**
   * assignInc    - creates the inclusion-tags for scripts assigned by {inc file='bla.css'} or inc('bla.css')
   * 
   * @access private
   * @return void
   */
  public function fetchInc(){
    $sutra  = sutra::get();
    $out    = "<!-- dynamically includes -->\n";
    foreach( $this->_data as $lang => $arr ){
      switch( $lang ){
        case "css": if( sutra::get()->yaml->cfg['global']['cache'] && is_object($sutra->compressor) )
                      $out .= $sutra->compressor->process( $arr, "css" );
                    else foreach( $arr as $file )
                      $out .= '    <link rel="stylesheet" href="'.$file.'" type="text/css" media="screen, projection">'."\n";  
                    break;
                    
        case "js" : if( sutra::get()->yaml->cfg['global']['cache'] && is_object($sutra->compressor) )
                      $out .= $sutra->compressor->process( $arr, "js" );
                    else foreach( $arr as $file )
                      $out .= '    <script type="text/javascript" src="'.$file.'"></script>'."\n";                             
                    break;
      }
    }
    return $out;
  }

 /**
  * assignInline  - creates the inclusion-tags for inline scripts assigned by {inline type="js"} or inline('alert("hoi")')
  *
  * @access private
  * @return void
  */
  private function fetchInline(){
    $js     = $jsDomReady = $css = "";
    $sutra  = sutra::get();
    $out    = "<!-- dynamically inline code -->\n";
    foreach( $this->_inlines as $lang => $arr ){
      foreach( $arr as $code ){
        switch( $lang ){
          case "css": $css  .= $code; break;
          case "js" : $js   .= $code; break;
          case "jsDomReady" : $jsDomReady  .= $code; break;
        }
      }
    }
    $out  .= '    <link rel="stylesheet" type="text/css" media="screen, projection">'.$css."</style>\n";
    $out  .= "    <script type=\"text/javascript\"> \nbaseurl = 'http://{$sutra->_url}';\n{$js}\nfunction onDomReady(){\n". $jsDomReady ."\n}\n</script>\n";
    return $out;
  }

  public function _call( $params, $add ){
    _assert( strlen( $params['function'] ) && strlen( $params['mod'] ), "please use 'call'-function correctly like : \{call mod='yourmod' function='yourFunctionName'}");
    $mod        = $params['mod'];
    $function   = $params['function'];
    return sutra::get()->mod->$mod->$function();
  }

  /*
   * fetchPage    - renders a page object of template file
   */
  public function process( &$page ){
    $sutra = sutra::get();
    $path  = sutra::get()->_path;
    parent::assign( "page", (array)$page );
    $this->template_dir = "{$path}/tpl/front";
    $page->html_main    = parent::fetch( "{$page->tpl_master}" );
    $this->assign("inc",    $this->fetchInc() ); 
    $this->assign("inline", $this->fetchInline() );
    $page->html_head    = parent::fetch( "{$page->tpl_head}" );
    $page->html_foot    = parent::fetch( "{$page->tpl_foot}" );
    $this->template_dir = sutra::get()->_path;
  }

  /**
   * fetch                  - this function is a man-in-the-middle function before 
   *                          the actual fetch() is called. It checks for additional
   *                          filters or patches.
   *                          The /lib/cms/admin/tpl/container.*.tpl templates check 
   *                          for these vars so templates can easily be 'patched/customized'
   *                          without changing the core.
   * @param mixed $file 
   * @access public
   * @return void
   */
  public function fetch( $file ){
    $sutra = sutra::get();
    if( !strstr( $file, $sutra->_path ) && $file[0] == "/" )
      $file  = "{$sutra->_path}{$file}";
    else if( !strstr( $file, $sutra->_path ) && $file[0] != "/" )
      $file  = "{$this->template_dir}/{$file}";
    _assert( is_file($file), "could not find file '{$file}'" );
    // lets fire an event, so listeners can customize the final output by assigning/modifing extra template variables
    $this->args          = !isset($this->args) ? array( "custom" => array(), "filter" => array(), "link" => array()  ) : $this->args;
    $this->args['var']   = $this->_vars;
    $this->args['file'] = $file;
    $this->assignDefaults();
    sutra::get()->event->fire( "SUTRA_TPL_FETCH", &$this->args );
    // since other listeners were able to massage/add template variables, lets fetch the template
    foreach( $this->args as $key => $value )
      $this->assign( $key, $value );
    $output = parent::fetch( $this->args['file'] );
    return $output;
  }

  public function translate( $id, $configfile = false ){
    if( $configfile )
      $this->config_load( $configfile );
    return $this->_confs[ $id ];
  }

  /**
   * correctUrls        
   * 
   * @param mixed $content 
   * @param mixed $smarty 
   * @access public
   * @return void
   */
  public function correctUrls( $content ){
    $rootdir = sutra::get()->yaml->cfg['global']['rootdir'];
    $content = preg_replace("/href=['\"]\/([^'\"]*)['\"]/i","href='/{$rootdir}/\\1'", $content );
    $content = preg_replace("/src=['\"]\/([^'\"]*)['\"]/i","src='/{$rootdir}/\\1'", $content );
    $content = preg_replace("/action=['\"]\/([^'\"]*)['\"]/i","action='/{$rootdir}/\\1'", $content );
    $content = preg_replace("/url\(['\"]\/([^'\"]*)['\"]/i","url('/{$rootdir}/\\1'", $content );
    // remove doubles *FIXME* because of bad ereg
    $content = str_replace( "{$rootdir}/{$rootdir}", $rootdir, $content );
    return $content;
  }
}

?>
