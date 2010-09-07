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
 * @package sutra framework
 */

class commentmanagerWidgets{

  public function testWidget( $args ){
    $sutra    = sutra::get();
    return $sutra->tpl->fetch( "widget.commentmanager.tpl" );
  }

}


?>
