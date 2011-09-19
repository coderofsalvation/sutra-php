<?
/** 
 * File:        class.page.php
 * Date:        05-10-2009
 *
 * container class of a page
 * 
 * Changelog:
 *
 * 	[Mon Oct  5 23:09:49 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * @package sutra framework 
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

class page{
  
  // attributes
  var $title;                                         // title of the page
  var $description;																		// description (SEO)
  var $keywords;																			// keywords (SEO)

  // structure
  var $tpl_head       = "head.tpl";                   // header
  var $tpl_master     = "index.tpl";                  // which mastertemplate?
  var $tpl_foot       = "foot.tpl";                   // footer

  // content
  var $content        = "";                           // the main content (not used when using CMS, use yaml instead)
  var $yaml           = array( 'content_1' => "" );   // content which is content manageble by cms
}
?>
