<?
/** 
 * File:        class.event.php
 * Date:        03-12-2009
 *
 * Event class. Handles listeners and fires events among modules & libs.
 * Also enables automatic event triggering thru webargs :
 * 
 *   http://yoursite.com/?event=YOUR_EVENT_NAME 
 *
 * Changelog:
 *
 * 	[Thu Dec  3 16:01:50 2009] 
 *		first sketch from scratch
 *
 * @todo 
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package sutra
 */

class event{

  public    $listeners        = array();

  public function __construct(){  
    $sutra = sutra::get();
    $this->addListener( "SUTRA_LOAD_USER", $this, "fireWebEvent" );
    // if cli command 'sutra_inspect' is present add to provide info
    if( isset( $sutra->cli ) ) $this->addListener( "SUTRA_LIB_EVENT_FIRE", &$sutra->cli, "onEvent" );
  }

  /**
   * executeUrl       - (interface function @ lib/cms/iface.urlListener.hp )
   *                    this makes this lib part of the url routing.
   *                    this function is called by sutra before any output started.
   *                    this is the 'web' autotrigger function, urls like http://yoursite.com/?event=YOUR_EVENT_NAME
   *                    will automatically fire event 'YOUR_EVENT_NAME' thru the sutra system
   * @param array $url 
   * @access public
   * @return void
   */
  public function fireWebEvent( $url ){
    // check shortcut to trigger debug window
    $args  = array_merge( $_POST, $_GET );
    if( isset( $args['event'] ) )
      $this->fire( $args['event'] );
  }

  /** 
   * fire             - with this function an event can be fired, which will travel across all the modules who
   *                    have registered the '$eventName' in their <eventListener>-tag in /mods/yourmod/cfg/config.xml
   *
   * *TODO* make 'event' lib (core/cms) with addEventListener() & fireEvent()
   *
   * @param string $var description 
   * @return mixed The new value 
   */ 

  public function fire( $eventName, $args = false, $caller = false )
  {
    $this->fireListeners(  $eventName, $args, $caller );
  }

  /**
   * fireListeners             - loop thru local listener table, and fire events when a listening subject is found
   * 
   * @param string $eventName  (uppercase divided by underscores like: I_DID_SOMETHING )
   * @param mixed $args        arguments to pass to listener function
   * @param mixed $caller      argument to identify caller
   * @access private
   * @return void
   */
  private function fireListeners( $eventName, &$args = false, $caller = false ){
    if( isset( sutra::get()->cli ) && $eventName != "SUTRA_LIB_EVENT_FIRE" ) 
        $this->fire( "SUTRA_LIB_EVENT_FIRE", array( "event" =>  $eventName, "listener" => false ), $caller  );
    if( !isset( $this->listeners[ $eventName ] ) ) return;
    foreach( $this->listeners[ $eventName ] as $key => $listener ){
      // if target is not an target, include file + class and replace target with object
      if( is_array( $listener->target ) ){
        include_once( $listener->target['file'] );
        $this->listeners[ $key ]->target = $listener->target = new $listener->target['class'];
      }
      if( method_exists( $listener->target, $listener->targetFunction ) ){
        if( isset( sutra::get()->cli ) && $eventName != "SUTRA_LIB_EVENT_FIRE" ) 
          $this->fire( "SUTRA_LIB_EVENT_FIRE", array( "event" =>  $eventName, "listener" => $listener ), $caller  );
        call_user_func( array( &$listener->target, $listener->targetFunction ), &$args, $caller );
      }
    }
  }

  /**
   * addListener                - here objects can register theirselves to fire a member function 
   *                              whenever an event is fired.
   * @param mixed $eventName 
   * @param mixed $target 
   * @param mixed $targetFunction 
   * @access public
   * @return void
   */
  public function addListener( $eventName, $target, $targetFunction ){
    _assert( strlen( $eventName ) && strlen( $targetFunction ), "addListener() no valid args! : {$eventName}:obj:{$targetFunction}");
    if( !isset( $this->listeners[ $eventName ] ) )
      $this->listeners[ $eventName ] = array();
    $this->listeners[ $eventName ][]  = (object)array(  "target"          => $target,
                                                        "targetFunction"  => $targetFunction,
                                                        );
  }
}

?>
