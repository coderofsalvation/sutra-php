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

class categorymanagerEventListener{
  
  public function getPage( $args, $caller ){
    $sutra = sutra::get();
    $url   = $sutra->url->get();
    if( is_array($url) && $url[0] == "category" ){
      $pageManager = new dbObject( "sutra_page" );
      $category    = new dbObject( "sutra_category" );
      $category->load( "title_url", $url[1] );
      $category->pages = $sutra->db->getArray( "SELECT * FROM `sutra_page` WHERE `yaml` LIKE '%category: {$category->id}%'", false );
      $sutra->tpl->assign( "category", (array)$category );
      $sutra->page->title             = $category->title;
      $sutra->page->yaml['content_1'] = $sutra->tpl->fetch( "/mod/categorymanager/tpl/category.show.tpl" );
    }
  }

}
?>
