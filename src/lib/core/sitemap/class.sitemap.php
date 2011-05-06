<?
/** 
 * File:        class.sitemap.php
 * Date:        Thu 23 Sep 2010 02:29:27 PM CEST
 *
 * good starting point to generate a google sitemap
 * 
 * Changelog:
 *
 * 	[Thu 23 Sep 2010 02:29:27 PM CEST] 
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
 * @copyright 2010 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
 * @license AGPL
 *
 * Copyright (C) <#year#>  <#name#>
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
 * %license%
 */

class sitemap {

  var $data   = array(); // here the sites are stored in format: array("url"=>"","priority"=>0.5)

  public function __construct(){ 
    $sutra = sutra::get();
    $sutra->event->addListener("SUTRA_READY", $this, "checkUrl" );
  }

  public function checkUrl(){
    $sutra = sutra::get();
    $url   = $sutra->url->get();
    if( count($url) && $url[0] == "sitemap"){
      $sutra->event->fire("SUTRA_SITEMAP_GET_PAGES"); // gather pages in $this->data
      $sutra->close( $this->generateSitemap( $this->data ) );
    }
    if( count($url) && $url[0] == "sitemap-html"){
      $sutra->event->fire("SUTRA_SITEMAP_GET_PAGES_TREE"); // gather pages in $this->data
      $sitemap = $this->generateHTMLSitemap( $this->data );
			foreach( $sutra->page->yaml as $k => $v )
				$sutra->page->yaml[ $k ] = $sitemap;
    }
  }
  
	public function generateHTMLSitemap( $data ){
    $sutra = sutra::get();
    $html                =  "<h1>Sitemap</h1><br>\n";
    $html .= "<ul>\n";
    foreach( $data as $k => $page )
      $html .= "  <li><a href='{$page['url']}'>{$page['url']}</a></li>\n";
    $html .= "</ul>\n";
    return $html;
  }

  public function generateSitemap( $data, $stripFromUrl = "" ){
    $sutra = sutra::get();
    $xml                =   "<?xml version='1.0' encoding='UTF-8'?>\n";
    $xml                .=  "<urlset xmlns='http://www.google.com/schemas/sitemap/0.84'>\n";
    foreach( $data as $k => $page ){
      $xml .= "<url>\n";
      $xml .= str_replace( $stripFromUrl, "", "  <loc>http://{$sutra->_url}{$page['url']}</loc>\n");
      $xml .= "  <lastmod>" . date(DATE_ATOM,time()) . "</lastmod>\n";
      $xml .= "  <changefreq>daily</changefreq>\n";
      $xml .= "  <priority>" . ( isset( $page['priority'] ) ? str_replace(",",".","{$page['priority']}") : "0.5" ) . "</priority>\n";
      $xml .= "</url>\n";
    }
    $xml                .=  "</urlset>\n";
    return $xml;
  }

}
?>
