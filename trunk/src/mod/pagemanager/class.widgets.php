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

class pagemanagerWidgets{

  public function tree( $args ){
    $sutra    = sutra::get();
    // display listing
    $sutra->mod->pagemanager->assignTree( false );
    return $sutra->tpl->fetch( "widget.tree.tpl" );
  }

}


?>
