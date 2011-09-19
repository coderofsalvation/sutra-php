<?
/** 
 * File:        class.url.php
 * Date:        Thu Jun  4 20:48:32 2009
 *
 * splittfies url for working with SEO-friendly urls
 * 
 * Changelog:
 *
 * 	[Thu Jun  4 20:48:32 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   $params = sutra::get()->url->get();
 *   $params = sutra::get()->url->get("http://www.google.com/some/param/blah");
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

class url{

  private $params;
  public function __construct(){
    $this->params = false;
  }

  /** 
   * get           - returns the parameters of an url
   * 
   * @param string $url url 
   * @return mixed The new value 
   */ 
  public function get( $url = false ){
    if( $this->params )
      return $this->params;
    if( !$url )
      $url  = $_SERVER['REQUEST_URI'];
    $base = str_replace( array("/index.php","index.php"), "", $_SERVER['PHP_SELF'] );
    $url  = str_replace( array( $base, "#" ), "", urldecode($url) );
    _log("url=".$url);
    if( !strlen( $url ) )  return;
    $url  = ( $url[0] == "/" ) ? substr( $url, 1, strlen($url)-1 ) : $url;
    $url  = urldecode($url);
    if( ($offset = strpos( $url, "?" )) !== false )
      $url  = substr( $url, 0, $offset );
    if( strlen($url) == 0 )
      $this->params   = false;
    else
      $this->params   =  strstr( $url, "/" ) ? explode("/", $url ) : array($url);
    return $this->params;
  }

  /**
   * getRootUrl               - get the rooturl. Sometimes when you are in a subdir, or the website is in a subdir,
   *                            you want to know the actual rootdir is located.
   *
   * @param mixed $rootdir    if your website is located in a subdir, pass the subdir as a string
   * @access public
   * @return void
   */
  function getRootUrl( $rootdir = false){
    $rooturl       = $_SERVER['HTTP_HOST'] . str_replace( "index.php", "", $_SERVER['PHP_SELF'] );
    if( $rootdir && !stristr( $_SERVER['PHP_SELF'], $rootdir ) )
      $rooturl    .= $rootdir;
    return $rooturl;
  }

}

?>
