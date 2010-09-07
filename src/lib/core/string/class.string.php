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
 * @package ...
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
    if ( !ereg( "[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}", $ip_address ) ) return false;
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
}

?>
