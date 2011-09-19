<?php
/** 
 * File:        class.string.php
 * Date:        Tue Mar  9 17:49:16 2010
 *
 * description 
 * 
 * Changelog:
 *
 * 	[Tue Mar  9 17:49:16 2010] 
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

class string{
  
  /**
   * converts a string into a Search Engine Optimized String
   *
   * @param string $str
   * @access public
   * @return string
   */
  public static function hyphenate( $str )
  {
    $str = strtolower( $str );
    $str = preg_replace( "/[^a-z0-9 -]/", "", $str );
    $str = str_replace( " ", "-", $str );
    return $str;
  }

  /**
   * See if given string is a valid email address
   *
   * @param string $email  The string to test
   * @return bool  true if the string is a valid email address
   */
  public static function isEmail( $email )
  {
    return eregi("^[0-9a-z]([-_.]*[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,4}$", $email );
  }

  /**
   * Checks if the given string is a valid ip address
   *
   * @param string $ip_address  ip address to check
   * @return boolean  True if the string is a valid ip address
   */
  public static function isIp ( $ip_address )
  {
    if ( strlen( $ip_address ) < 8 ) return false;
    $parts = explode( '\.', $ip_address );
    if ( count( $parts ) != 4 ) return false;
    foreach( $parts as $int ) {
      $int = (int) $int;
      if ( $int < 0 || $int > 254 ) return false;
    }
    if ( !preg_match( "/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/i", $ip_address ) ) return false;
    return true;
  }

  /**
   * parseToggleString        - interpret a string which contains flags like "+cola, +rum, -sugar"
   * 
   * @param mixed $str        - for example "+enabledFlag, -disabledFlag"
   * @param mixed $glue       - for example ","
   * @access public
   * @return array            array( "enabled" => array, "disabled" => array )
   */
  function parseToggleString( $str, $glue ){
    _assert( strlen($glue), "glue not set!");
    $enabled  = array();
    $disabled = array();
    $str      = str_replace(" ","", $str );
    $tokens   = explode( $glue, $str );
    foreach( $tokens as $token )
      if( $token[0] != "-" )  $enabled[]  = ( $token[0] != "+" ) ? $token : substr( $token, 1, strlen($token)-1 );
      else                    $disabled[] = substr( $token, 1, strlen($token)-1 );
    return array( "enabled" => $enabled, "disabled" => $disabled );
  }

	/**
	 * xss - protect the strings inside an array or object agains xss values
	 * 
	 * @param mixed $mixed 
	 * @access public
	 * @return void
	 */
	function xss($mixed)
	{
		if (is_array($mixed)) {
			foreach($mixed as $key => $value) {
				if (is_array($value) || is_object($value)) $mixed[$key] = xss($value);
				else $mixed[$key] = htmlspecialchars($value);
			}
		} elseif (is_object($mixed)) {
			$assoc_obj_vars = get_object_vars($mixed);
			foreach($assoc_obj_vars as $key => $value) {
				if (is_array($value) || is_object($value)) $mixed->$key = xss($value);
				else $mixed->$key = htmlspecialchars($value);
			}
		} else {
			$mixed = htmlspecialchars($mixed);
		}
		return $mixed;
	}

}

?>
