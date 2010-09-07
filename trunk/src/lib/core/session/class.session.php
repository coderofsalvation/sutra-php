<?
/** 
 * File:        class.session.php
 * Date:        Wed Mar 10 17:18:04 2010
 *
 * session handler class
 * 
 * Changelog:
 *
 * 	[Wed Mar 10 17:18:04 2010] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package ...
 */

class session{

  public $sessionId = false;

  public function __construct(){
    $this->init();
    // lets call close() when SUTRA_CLOSE event is fired
    sutra::get()->event->addListener( "SUTRA_CLOSE", $this, "close" );
  }

  public function init(){
    $sutra      = sutra::get();
    // re-enable session (AJAX is asynchronous)
    if ( isset( $_GET['SESSION_ID'] ) && strlen( $_GET['SESSION_ID'] ) == 32 && ereg( "[A-z0-9]", $_GET['SESSION_ID'] ) ) 
      session_id( $_GET['SESSION_ID'] );
    if ( isset( $_POST['SESSION_ID'] ) && strlen( $_POST['SESSION_ID'] ) == 32 && ereg( "[A-z0-9]", $_POST['SESSION_ID'] ) ) 
      session_id( $_POST['SESSION_ID'] );
    if( !isset( $sutra->cli ) ){
			session_set_cookie_params('3600'); // 1 hour
      session_start();
	  }
    $sutra->tpl->assign( "sessionId", ($this->sessionId = session_id()) );
    $sutra->tpl->inline("js", "// (sutra/event) ajax synchronous call should share this SESSION_ID as post/get var\nvar SESSION_ID = '{$this->sessionId}';\n" );
  }

  public function close(){
    session_write_close();
  }

}

?>
