<?
/** 
 * File:        class.widget.php
 * Date:        16-08-2009
 *
 * represents the Front End Request handler for widgets
 * if urls start with the form 'widget/yourmodule/yourwidgetfunction' then this library will immediately 
 * return widgetcontent to the browser.
 *
 * What is a widget? A widget is a small piece of PHPcode + template which make up a page. Defined in /mod/yourmod/class.widgets.php
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
 *  [SMARTY]
 *   {widget mod="mymodule" name="myWidget}
 *   {widget file="/custom/widget/someWidget.php"}
 *   {widget url="/news/item-1" [height,width,section,class]}
 *   {widget url="http://www.google.com" [height,width,class]}
 * </code>
 *
 * @package sutra
 * @license
 *  *
 * Copyright (C) 2011, Sutra Framework < info@sutraphp.com | www.sutraphp.com >
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *

 */

class widget{

  public function __construct(){ 
    sutra::get()->tpl->register_function( "widget", array( &$this, "tplFetchWidget"));
    sutra::get()->event->addListener( "SUTRA_URL", $this, "executeUrl" );
  }

  /**
   * executeUrl          -  this function is called by sutra before any output started.
   * 
   * @param array $url 
   * @access public
   * @return void
   */
  public function executeUrl( $url ){
    if( $url[0] != "widget" ) return;
    // create module,widgets & arguments object
    $sutra          = sutra::get();
    $sutra->event->fire( "SUTRA_EXECUTE_WIDGET" );
    $url            = $sutra->url->get();
    $content        = $this->fetchWidget( $url[1], $url[2] );
    $sutra->close( $content );
  }

  public function fetchWidget( $module, $function, $browserRequest = true ){
      $sutra          = sutra::get();
      $url            = $sutra->url->get();
      $widgets        = is_object( $sutra->mod->$module ) ? $sutra->mod->$module->widgets : false;
      $args           = is_array($url) ? array_splice( $url, 3 ) : array();
      _assert( $widgets, "Could not create widgets object for module '{$module}'" );
      $allowed        = $this->isAllowed( $module, $function );
      if( $browserRequest && !$allowed )
         $sutra->close( "Sorry...you are not allowed to view this widget" );
      if( !$allowed ) return false;
      if( !is_callable( array( $widgets, $function ) ) )
        $sutra->close("could not call/find widget function '{$function}' for module '{$module}'");
      $template_dir_bak         = $sutra->tpl->template_dir;
      $sutra->tpl->template_dir = $sutra->_path . "/mod/{$module}/tpl";
      $output                   = call_user_func( array( $widgets, $function ), $args );
      $sutra->tpl->template_dir = $template_dir_bak;
      return $output;
  }

  /**
   * isAllowed       - check if this is a public widget (we don't want curious people to get system widgets)
   *                   NOTE : a widget is restricted if there is a 'permissions' field present in the 
   *                          config.xml of the module (at section 'widgets')
   * @param mixed $module 
   * @param mixed $widget 
   * @access public
   * @return void
   */
  public function isAllowed( $module, $function ){
    $sutra          = sutra::get();
    $allowed        = true;
    $publicWidgets  = $sutra->mod->$module->cfg['system']['widgets'];
    foreach( $publicWidgets as $widget ){
      if( $widget['name'] == $function ){
        if( !isset( $widget['permissions'] ) ) $allowed = true;
        else{
          $permissions = explode( ",", trim( $widget['permissions'] ) );
          _log($permissions);
          foreach( $permissions as $permission )
            $allowed =& $sutra->acl->isAllowed( $permission );
        }
      }
    }
    return $allowed;
  }
  
  /**
   * tplFetchWidget          - this function is called thru the templates {widget mod="mymodule" name="myWidget} tag
   *                                                                   or {widget file="/custom/widget/someWidget.php"}
   *                                                                   or {widget url="/news/item-1" [height,width]}
   *                                                                   or {widget url="http://www.google.com" [height,width]}
   * 
   * @param mixed $params 
   * @access public
   * @return void
   */
  public function tplFetchWidget( $params, $tpl ){
    $sutra                    = sutra::get();
    $file                     = isset( $params['file'] ) ? "{$sutra->_path}/{$params['file']}" : false;
    $modWidget                = !empty( $params['mod'] ) && !empty( $params['name'] );
    $url                      = !empty( $params['url'] );
    $template_dir_bak         = $sutra->tpl->template_dir;
    $sutra->tpl->template_dir = $sutra->_path;
    if( $file ) 
      $out = $this->tplFetchWidgetFile( $params, $tpl, $file );
    if( $modWidget )
      $out = $this->tplFetchWidgetMod( $params, $tpl );
    if( $url )
      $out = $this->tplFetchWidgetUrl( $params, $tpl );
    $sutra->tpl->template_dir = $template_dir_bak;
    return $out;
  }

  private function tplFetchWidgetFile( $params, $tpl, $file ){
    if( _assert( is_file( $file ), "could not find/execute file '{$file}'" ) ){
      ob_start();
      include_once( $file );
      $html = ob_get_contents();
      ob_end_clean();
      return $html;
    }else return false;
  }

  private function tplFetchWidgetMod( $params, $tpl ){
    $sutra          = sutra::get();
    foreach( $params as $key => $param )
      if( $param != "name" && $param != "mod" )
        $sutra->tpl->assign( $key, $param );
    return $this->fetchWidget( $params['mod'], $params['name'], false );
  }

  /**
   * tplFetchWidgetUrl 
   * 
   * @param mixed $params 
   * @param mixed $tpl 
   * @access private
   * @return void
   */
  private function tplFetchWidgetUrl( $params, $tpl ){
    $sutra      = sutra::get();
    $url        = $params['url'];
    $local      = ($url[0] == "/");
    $class      = isset( $params['class'] )   ? $params['class']    : "";
    $section    = isset( $params['section'] ) ? $params['section']  : "content_1";
    $remote     = strstr( $url, "http://" );
    _assert( $remote || $local, "widget tag must have url attribute starting with '/' or 'http://'");

    if( $local )  $page     = new dbObject( "sutra_page" );
    $div        = "<div class='%class% editable' id='%id%' %height% %width%>%content%</div>";
    $div        = str_replace( "%height%",  isset( $params['height'] ) ? "height='{$params['height']}'" : "", $div );
    $div        = str_replace( "%width%",   isset( $params['width'] )  ? "width='{$params['width']}'"   : "", $div );
    $div        = str_replace( "%content%", $local ? $page->load( "title_url_path", $url )->yaml[ $section ] : file_get_contents( $url ), $div );
    $div        = str_replace( "%id%",      $local ? $section : $sutra->string->hyphenate( $url), $div );
    $div        = str_replace( "%class%",   $class . ($local ? $page->id : $sutra->string->hyphenate( $url)), $div );
    if( $local ) _assert( strlen( $page->yaml[ $section ] ), "Widget (url) local page '{$url}' section '{$section}' is empty...is this ok?" );
    return $div;
  }
}



?>
