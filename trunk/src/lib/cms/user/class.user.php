<?php
/** 
 * File:        user.php
 * Date:        Mon Mar 22 20:09:01 2010
 *
 *
 */
class user{

  public $username;
  public $password;
  public $firstname;
  public $lastname;
  public $group = "user"; 

  public function load( $id ) {
    _assert( is_numeric( $id ), "user::load( userid ) invalid integer!");
    $sutra  = sutra::get();
    $user   = new dbObject( "sutra_user" );
    $user->load( "id", $id );
    $user->copyTo( &$this );
    $sutra->event->fire( "SUTRA_LOAD_USER", $id, $this  );
  }
}
?>
