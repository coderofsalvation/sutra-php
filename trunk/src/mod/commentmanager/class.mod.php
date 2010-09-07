<?
/** 
 * File:        class.mod.php
 * Date:        16-08-2009
 *
 * represents a class module
 * 
 * Changelog:
 *
 * 	[Sat Aug 15 22:54:27 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   $sutra->mod->page
 * </code>
 *
 * @package sutra Framework 
 */

// lets import a decorator class for sql table 'sutra_page_comment' 
include_once( dirname(__FILE__)."/dbObject/sutra_page_comment.php" );

class commentmanager extends abstractMod{

  public function __construct(){
    parent::init($this);
    sutra::get()->tpl->register_function("loadcomment",     array( &$this, "smartyLoadComment" ) );
  }

  /**
   * loadComments 
   * 
   * @param mixed $offset      - sometimes passed by datagrid for pagination
   * @param mixed $amount       - sometimes passed by datagrid for pagination
   * @param mixed $returnCount - returns count of all records (needed by datagrid for pagination)
   * @access public
   * @return void
   */
  public function loadComments( $offset = false, $amount = false, $returnCount = false ){
    $dbObject = new dbObject("sutra_page_comment");
		if( $returnCount ) return $dbObject->countAll();
		if( $amount || $offset ){
			return $dbObject->loadByProperty( "id", "*", "date", "ASC", $amount, $offset );
	  }
		else return $dbObject->loadAll();
  }

	public function smartyLoadComment(){
		$input = array_merge( $_POST, $_GET );
		$sutra = sutra::get(	);
		if( _assert( isset( $input['id'] ), "please need 'id' variable from _GET or _POST") ){
			$obj = new dbObject( "sutra_page_comment" );
			$obj->load( "id", (int)$input['id'] );
			$sutra->tpl->assign("comment", $obj );
		}
	}

}
?>
