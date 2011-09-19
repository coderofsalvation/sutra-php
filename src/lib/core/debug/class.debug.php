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
