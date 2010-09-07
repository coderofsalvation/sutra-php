<?
/** 
 * File:        class.eventListener.php
 * Date:        #date#
 *
 * here the events for module commentmanager are implemented
 * 
 * Changelog:
 *
 * 	#date#
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package Sutra Framework 
 */

class commentmanagerEventListener{

  public function getPage( $page ){
    $sutra    = sutra::get();
    //$msg      = "You have been pimped by commentmanager module!!"; 
    //$page->title              = $msg;
    //$page->yaml['content_1'] .= "<br>{$msg}";
  }

  public function deleteComment( $args ){
    $sutra = sutra::get();
    $url   = $sutra->url->get();
    $parms = array_merge( $_POST, $_GET );
    $obj   = new dbObject( "sutra_page_comment" );
    $obj->load( 'id', $parms['id'] );
    if( $obj->id ){
      $sutra->tpl->assign("delete_succes", true );
      $obj->delete();
    }else{
      $sutra->tpl->assign("delete_succes", false );
    }
  }

	public function updateCommentVar(){
		$sutra = sutra::get();
		$input = array_merge( $_GET, $_POST );
		$obj   = new dbObject('sutra_page_comment');
		$obj->load( 'id', (int)$input['id'] );
		unset($input['id']);
		unset($input['event']);
		foreach( $input as $k => $v )
			if( isset( $obj->$k ) )
				$obj->$k = $v;
		if( $sutra->acl->isAllowed( "SUTRA_MOD_WRITE" ) )
			$obj->save();
	}
}


?>
