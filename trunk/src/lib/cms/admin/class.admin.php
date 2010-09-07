<?php
class admin{

  public function __construct(){
    $sutra    = sutra::get();
    $sutra->event->addListener( "SUTRA_READY", $this, "init" );
    $sutra->tpl->register_block( "uicontainer",       array( &$this, "tplAdminContainer"));
    $sutra->tpl->register_block( "uicomponent",       array( &$this, "tplAdminComponent"));
    $sutra->tpl->register_function( "uinotify",      array( &$this, "tplAdminNotify"));
    $sutra->nicedit;
  }

  public function init(){
    $sutra    = sutra::get();
    if( $sutra->acl->isAllowed( "SUTRA_VIEW_PANEL" ) ){
      $sutra->event->fire( "SUTRA_VIEW_PANEL" );
      $sutra->tpl->assign("panelIcons", $this->generateIcons() );
      if( ($url = $sutra->url->get()) && count($url) && $url[0] == "togglecache" )
        $this->toggleCache();
    }
    $sutra->tpl->assign("cache", $sutra->yaml->cfg['global']['cache'] );
    $sutra->tpl->assign("panel", $sutra->tpl->fetch( "lib/cms/admin/tpl/panel.tpl" ) );
  }

  public function toggleCache(){
    $sutra    = sutra::get();
    $cfg      = $sutra->yaml->cfg;
    $cfg['global']['cache'] = !$cfg['global']['cache'];
    $cacheDir = $sutra->_path."/data/cache";
    if( !$cfg['global']['cache'] ){
      $files    = $sutra->files->getDir( $cacheDir );
      foreach($files as $file )
        unlink( $cacheDir."/".$file );
    }
    file_put_contents( $sutra->_path . "/data/cfg.yaml.php", $sutra->yaml->dump( $cfg ) ); 
  }

  private function generateIcons(){
    $sutra      = sutra::get();
    $mods       = $sutra->mod->getModules();
    $noWeight   = 0;
    $modsIcons  = array();
    $maxweight  = 0;
    foreach( $mods as $name => $mod ){
      if( isset( $mod['system']['icon_hide'] ) && $mod['system']['icon_hide'] )
        continue;
      if( !isset( $mod['system']['weight'] ) && ++$noWeight ) 
        $mod['system']['weight'] = $noWeight;
      $weight     = isset( $modsIcons[ $mod['system']['weight'] ] ) ? $maxweight+1 : $mod['system']['weight'];
      $maxweight  = ( $weight > $maxweight ) ? $weight : $maxweight;
      $modsIcons[ $weight ] = array();
      $modsIcons[ $weight ][ 'info' ] = $mod['system']['info'];
      $modsIcons[ $weight ][ 'name' ] = $name;
      $modsIcons[ $weight ][ 'icon' ] = "/mod/{$name}/{$mod['system']['icon']}";
      $modsIcons[ $weight ][ 'link' ] = "/{$name}/backend";
    }
    ksort( $modsIcons );
    return $modsIcons;
  }

  
  /**
   * notify                 - shortcut function for the succes/error messages in the templates
   * 
   * @param mixed $language_var 
   * @access public
   * @return void
   */
  public function notify( $language_var ){
    $_GET[ $language_var ] = true;
    sutra::get()->tpl->assign( "_GET", $_GET );
  }

  /**
   * tplAdminContainer 
   * 
   * example usage:     {uicontainer    type="normal" title="Skeletontje" description="this is a description" filters="put more filters here"}
   *                      this is content
   *                    {/uicontainer}
   * @param mixed $params   
   * @param mixed $content 
   * @access public
   * @return void
   */
  public function tplAdminContainer( $params, $content ){
    if( strlen($content) ){
      _assert( isset( $params['type'] ),"which type of container do you want? big/normal etc" );
      $sutra    = sutra::get();
      $_params  = array( "container_content" => $content );
      $tplFile  = "lib/cms/admin/tpl/container.{$params['type']}.tpl";
      // create unique namespace
      foreach( $params as $k => $v )
        $_params[ "admin_{$k}" ] = $v;
      $sutra->tpl->assign( $_params );
      return $sutra->tpl->fetch( $tplFile );
    }
  }

  /**
   * tplAdminComponent 
   * 
   * example usage :      {uicomponent  type="normal" help="helptext" label="This is a label" label_id="path" advanced=false}     
   *                        <input type="textfield" name="bla">               
   *                      {/uicomponent}
   * @param mixed $params 
   * @param mixed $content 
   * @access public
   * @return void
   */
  public function tplAdminComponent( $params, $content ){
    if( strlen($content) ){
      $sutra    = sutra::get();
      $_params  = array( "component_content" => $content );
      $tplFile  = "lib/cms/admin/tpl/component.{$params['type']}.tpl";
      // create unique namespace
      foreach( $params as $k => $v )
        $_params[ "admin_{$k}" ] = $v;
      $sutra->tpl->assign( $_params );
      $out      = $sutra->tpl->fetch( $tplFile );
      foreach( $_params as $k => $v )
        $sutra->tpl->assign( $k, false );
      return $out;
    }
  }

  /**
   * tplAdminNotify    - displays a colored understandable message for the user
   * 
   * example usage :      {uinotifybox type="succes"   content="Hello world!"}
   *                      {uinotifybox type="error"    content="I was never born!"}
   *                      {uinotifybox type="warning"  content="Be carefull!" duration=2000}  <- show for 2 seconds
   *                      {uinotifybox type="ask"      content="Are you sure?"}
   *
   * @param mixed $params 
   * @access public
   * @return void
   */
  public function tplAdminNotify( $params ){
    $sutra    = sutra::get();
    $tplFile  = "lib/cms/admin/tpl/notify.tpl";
    // create unique namespace
    foreach( $params as $k => $v )
      $_params[ "admin_{$k}" ] = $v;
    $sutra->tpl->assign( $_params );
    $sutra->tpl->assign( "uinotify", $sutra->tpl->fetch( $tplFile ) );
  }


}
?>
