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
 * @package sutra 
 * @license 
 *  *
 * Copyright (C) 2011, Sutra Framework < info@sutraphp.com | www.sutraphp.com >
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *

 */

class session{

  public $lifeTime  = 5400; // 1,5 hour
  public $sessionId = false;

  public function __construct(){
    $this->init();
    // lets call close() when SUTRA_CLOSE event is fired
    sutra::get()->event->addListener( "SUTRA_CLOSE", $this, "close" );
  }

  public function init(){
    $sutra      = sutra::get();
    // re-enable session (AJAX is asynchronous)
    if ( isset( $_GET['SESSION_ID'] ) && strlen( $_GET['SESSION_ID'] ) == 32 && preg_match( "/[A-z0-9]/i", $_GET['SESSION_ID'] ) ) 
      session_id( $_GET['SESSION_ID'] );
    if ( isset( $_POST['SESSION_ID'] ) && strlen( $_POST['SESSION_ID'] ) == 32 && preg_match( "/[A-z0-9]/i", $_POST['SESSION_ID'] ) ) 
      session_id( $_POST['SESSION_ID'] );
    if( !isset( $sutra->cli ) ){
			session_set_cookie_params( $this->lifeTime );
      session_start();
	  }
    setcookie(session_name(),session_id(),time()+$this->lifeTime);
    $sutra->tpl->assign( "sessionId", ($this->sessionId = session_id()) );
    $sutra->tpl->inline("js", "// (sutra/event) ajax synchronous call should share this SESSION_ID as post/get var\nvar SESSION_ID = '{$this->sessionId}';\n" );
  }

  public function close(){
    session_write_close();
  }

}

?>
