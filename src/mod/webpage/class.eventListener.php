<?
/** 
 * File:        <#file#>.php
 * Date:        <#date#>
 *
 * description 
 * 
 * Changelog:
 *
 * 	[Sun Aug 16 01:39:43 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package IZIFramework 
 */

class webpageEventListener{

  /*
   * getPage             - this function is called by the SUTRA_READY event. It will :
   *                        
   *                        1) check if this page exists in the database
   *                           (column 'title_url' in sqltable 'sutra_page')
   *                                    ^-- /mod/webpage/tpl/pagename.tpl
   *
   *
   * @param mixed $args 
   * @param mixed $caller 
   * @access public
   * @return void
   */
  public function getPage( $args, $caller ){
    if( !is_array( $args ) ) $args = array();
    $page                        = false;
    $sutra                       = sutra::get();
    $url                         = $sutra->url->get();
    $title_url_path              = empty( $args ) && is_array( $url ) ? implode("/", $url ) : false;
    // if url equals '/admin' in url, load previous page so they can start editing
    $title_url_path              = $title_url_path == "admin" && isset($_SESSION['title_url_path'])? substr( $_SESSION['title_url_path'], 1) : $title_url_path;
    // if url ends with '/admin', strip it from the url
    $title_url_path              = strstr( $title_url_path, "/admin") ? str_replace("/admin","",$title_url_path) : $title_url_path;
    // if the sessionsvar 'title_url' has mysterious values, then you might check the allowable extensions in .htaccess (unsuccesfull requests can mess this up)
    if( strlen( $title_url_path ) && $title_url_path != "admin" ){
      $title_url_path              = "/" . $title_url_path;
      $_SESSION['title_url_path']  = $title_url_path;
      $page        = $sutra->db->getObject( "SELECT * FROM `sutra_page` where `title_url_path` = '{$title_url_path}'" );
    }else $page = $sutra->mod->webpage->ensurePage( $page );
    // merge page values with sutra page object
    $sutra->event->fire( "SUTRA_PAGE_GET", &$page );
    $sutra->page   = (object)array_merge( (array)$sutra->page, (array)$page );
  }

  /* 
   * savePage           - save's a Page 
   * 
   * @param string $var description 
   * @return mixed The new value 
   */ 
  function savePage( $args, $caller )
  {
    $sutra          = sutra::get();
    $input          = $_POST;
    $ignoreVars     = array( "use_session", "event" );
    $use_session    = isset( $input['use_session'] );
    $title_url_path = $use_session ?  $_SESSION['title_url_path'] :  false;
    // remove vars to prevent them storing into the db
    foreach( $input as $k => $v )
      if( in_array( $k, $ignoreVars ) )
        unset( $input[ $k ] );
    // if page id is set in POST array then only update yaml section in other page
    // else just update page stored in session (last opened page)
    $page           = $sutra->mod->webpage->savePage( $input, 
                                                      $title_url_path, 
                                                      isset( $input['page_id'] ) ? $input['page_id'] : false, 
                                                      $use_session );
    // if page url is changed update session and let page refresh thru javascript
    if( $use_session && ($_SESSION['title_url_path'] != $page->title_url_path) && ( $_SESSION['title_url_path'] = $page->title_url_path ) )
      $sutra->ajax->getUrl( "http://{$sutra->_url}".substr( $page->title_url_path,1 ) . "?snippet=site", "site" );
    //die( "ok saved '{$_SESSION['title_url_path']}'" );
  }
}


?>
