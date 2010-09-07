<?
/** 
 * File:        class.mod.php
 * Date:        16-08-2009
 *
 * represents a module manager
 * 
 * Changelog:
 *
 * 	[Sat Aug 15 22:54:27 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   $sutra->mod->page
 * </code>
 *
 * @package $utra Framework 
 */
require_once( dirname(__FILE__)."/abstract.mod.php" );

class mod{

  private $_mods;

  public function __construct(){
    $sutra  = sutra::get();
    if( !is_array( $this->_mods ) )
      $this->scan();
    // register events
    $sutra->event->addListener( "SUTRA_URL", $this, "executeUrl" );
    $sutra->event->addListener( "SUTRA_INIT_MODULES", $this, "scanModuleListeners" );
    $sutra->event->addListener( "SUTRA_INIT_MODULES", $this, "autoCreateModules" );
  }

  public function getModules(){
    return $this->_mods;
  }

  public function setModules( $modules ){
    _assert( is_array( $modules ), "setModules() modules array is empty!" );
    $this->_mods = $modules;
  }

  /*
   * executeUrl          - interface function, will listen to url www.yoursite.com/modulename/pagename
   * @param mixed $url 
   * @access public
   * @return void
   */
  public function executeUrl( $url ){
    if( empty( $this->_mods[ $url[0] ] ) || count($url) != 2  ) return;
    $html  = "";
    $sutra = sutra::get();
    $sutra->event->fire( "SUTRA_EXECUTE_MOD" );
    $sutra->tpl->resetInc();
    $html .= $sutra->tpl->fetchInc();
    $html .= $this->showPage( $url[0], $url[1] );
    $sutra->close( $html );
  }

  public function __get( $var ){ 
    _assert( array_key_exists( $var, $this->_mods ), "Module {$var} does not exist!" );
    $sutra        = sutra::get();
    if( isset( $this->_mods[ $var ]['object'] ) ){
      return $this->_mods[ $var ]['object'];
    }else{
      $classFile = $sutra->_path . "/mod/{$var}/class.mod.php";
      $customDir = $sutra->_path . "/custom/mod/{$var}";
      $customs   = false;
      if( is_file( $classFile ) ){
        require_once( $classFile );
        // include customs
        if( is_dir( $customDir ) )
          if( ($customs = $sutra->files->getDir( $customDir ))  && is_array($customs) && count($customs) )
            foreach( $customs as $custom )
              include_once( "{$customDir}/{$custom}" );
        return ( $this->_mods[ $var ]['object'] = new $var() );
      }
    }
    return false;
  }

  /* 
   * scan       - scans for modules 
   * 
   * @param string $var description 
   * @return mixed The new value 
   */ 
  private function scan( )
  {
    $sutra        = sutra::get();
    $cache        = $sutra->_path . "/data/cache";
    $cacheFile    = "modsdir.cache";
    if( ! ( $this->_mods = $sutra->cache->get( "mods" ) ) ){
      $modsDir    = $sutra->_path . "/mod";
      $mods       = $sutra->files->getDir( $modsDir, false, false, true );
      $oldYamlDir = $sutra->yaml->getDir();
      // for every module read its config.xml, and convert it to an array, and finally to an object
      foreach( $mods as $mod ){
        $moduleDir    = "{$sutra->_path}/mod/{$mod}";
        $moduleCfgDir = $moduleDir."/cfg";
        $fileConfig   = $moduleCfgDir."/config.yaml.php";
        _assert( is_file( $fileConfig ), "no '{$fileConfig}' found in module {$mod} :[");
        $sutra->yaml->setDir( $moduleCfgDir );
        $this->_mods[ $mod ] = $sutra->yaml->config;
      }
      $sutra->yaml->setDir( $oldYamlDir );
      $sutra->cache->save( "mods", $this->_mods );
    }
  }

  /*
   * showPage  
   * 
   * @param mixed $modName 
   * @param mixed $pageName 
   * @access public
   * @return void
   */
  public function showPage( $modName, $pageName ){
    $sutra    = sutra::get();
    // NOTE: this assertion also inits the module class ( necesary for if you want to register template functions )
    _assert( isset( $this->_mods[ $modName] ) && is_object( $sutra->mod->$modName ), "Module '{$modName}' does not exist! :(");
    $sutra->event->fire( "SUTRA_SHOW_PAGE" );
    $tplFile  = null;
    $page     = null;
    $pages    = $this->_mods[ $modName ]['system']['pages'];
    foreach( $pages as $p )
      if( $p['name'] == $pageName )
        $page = $p;
    if( _assert( is_array($page), "{$modName} :: <pages> page '{$pageName}' not found!") ){
      $redirectHTML = "<script type='text/javascript'>document.location.href = 'http://{$sutra->_url}admin';</script>";
      if( $sutra->acl->areAllowed( $page['permissions'], $redirectHTML ) ){
        $tplFile    = sutra::get()->_path. "/mod/{$modName}/{$page['file']}";
        $pageExists = ( isset( $tplFile ) && is_file( $tplFile) );
        _assert( $pageExists, "{$modName} :: <pages> template file '{$tplFile}' does not exist!"  );
        $output = $sutra->tpl->fetch( $tplFile );
        _assert( strlen($output) > 1, "output is null when trying to fetch page '{$tplFile}', something strange is going on!");
      }
    }
    return $output;
  }

  /*
   * scanModuleListeners     - loop thru modules, and fire events when a listening module is found
   * 
   * @param string $eventName  (uppercase divided by underscores like: I_DID_SOMETHING )
   * @param mixed $args        arguments to pass to listener function
   * @param mixed $caller      argument to identify caller
   * @access private
   * @return void
   */
  public function scanModuleListeners()
  {
    $sutra    = sutra::get();
    $modules  = $this->_mods;
    foreach( $modules as $modName => $mod ){
      if( is_array($mod['system']['listenEvents'] ) ){
        foreach( $mod['system']['listenEvents'] as $event ){
          _assert( isset( $event['event'] ), "scanModuleListeners() could not event \$event['event'] in config.yaml.php of module '{$modName}'" );
          $eventClassFile = $sutra->_path."/mod/{$modName}/class.eventListener.php";
          $eventClassName = $modName . "EventListener";
          $lazyTarget     = array( "file" => $eventClassFile, "class" => $eventClassName );
          $sutra->event->addListener( $event['event'], $lazyTarget, $event['function'] );
        }
      }
    }
  }

  public function autoCreateModules(){
    $sutra = sutra::get();
    // auto create modules if necesary
    foreach( $this->_mods as $name => $mod )
      if( isset($mod['system']) && isset( $mod['system']['autocreate'] ) && $mod['system']['autocreate'] )
        $sutra->mod->$name;
  }


}

?>
