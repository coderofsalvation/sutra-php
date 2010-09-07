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
