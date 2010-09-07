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

class filemanagerWidgets{

  public function tree( $args ){
    $sutra    = sutra::get();
    // display listing
    $sutra->mod->filemanager->assignTree();
    return $sutra->tpl->fetch( "widget.tree.tpl" );
  }
  
  public function treePath( $args ){
    $sutra        = sutra::get();
    // get tree from filelist, only keep directories, and pass as arg for treeManager which converts it to indented list
    $tree         = $sutra->mod->filemanager->getTree( true );
    if( !$sutra->mod->filemanager->cfg['system']['upload_in_root'] )
      $tree       = is_array($tree) && is_array( $tree[0]['children'] ) ? $tree[0]['children'] : array();
    $treePath     = $sutra->treeManager->slapTree( $tree , 3, "name_short", "/", "&nbsp;" );
    $sutra->tpl->assign( "treePath", $treePath );
    return $sutra->tpl->fetch( "widget.treePath.tpl" );
  }

}


?>
