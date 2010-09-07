<?
/** 
 * File:        class.widgets.php
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
 * @package $utra framework
 */

class webpageWidgets{

  public function treePath( $args ){
    $sutra    = sutra::get();
    // get tree from db and pass as arg for treeManager which converts it  to indented list
    $treePath     = $sutra->treeManager->slapTree(  $sutra->mod->webpage->getTree(), 3, "title_menu", "/", "&nbsp;" );
    $sutra->tpl->assign( "treePath", $treePath );
    return $sutra->tpl->fetch( "widget.treePath.tpl" );
  }

}


?>
