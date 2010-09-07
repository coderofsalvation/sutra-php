<?
/** 
 * File:        class.debug.php
 * Date:        07-10-2009
 *
 * debugger class.
 * When enabled ( by calling _debug() or settings GET/POST variable 'debug' ) it will
 * include a javascript which will popup a console window.
 * 
 * Changelog:
 *
 * 	[Wed Oct  7 21:04:28 2009] 
 *		first sketch from scratch
 *
 * @package sutra framework 
 */

class debug{

  private $active;

  public function __construct(){
    $this->active = false;
    sutra::get()->event->addListener( "SUTRA_INIT_LIBS", $this, "executeUrl" );
  }

  /**
   * execute          -
   *                    this function is called by sutra before any output started.
   * 
   * @param array $url 
   * @access public
   * @return void
   */
  public function executeUrl( $url ){
    $input = array_merge( $_POST, $_GET );
    // check shortcut to trigger debug window
    if( isset( $input['debug'] ) )
      $_SESSION['debug'] = $input['debug'];
    if( isset( $_SESSION['debug'] ) && $_SESSION['debug'] ){
      $_URL = sutra::get()->url->get();
      ob_start();
      print_r(array(
                "sutra->url->get()" => $_URL,
                "\$_GET" => $_GET,
                "\$_POST" => $_POST,
                "\$_FILES" => $_FILES,
                "\$_SESSION" => $_SESSION,
                "\$_SERVER" => $_SERVER
              ));
      $out = ob_get_contents();
      ob_end_clean();
      _debug($out);
      _debug("\$utra manual debugging started");
      $this->activate();
    }
  }

  public function isActive(){ return $this->active; }

  public function activate(){
    if( !$this->active ){
      sutra::get()->tpl->inc( "/lib/core/debug/js/debug.js" );
      $this->active = true;
    }
  }
}

?>
