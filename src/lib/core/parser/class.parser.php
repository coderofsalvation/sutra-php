<?

/** 
 * File:        parser.php
 * Date:        Tue 14 Apr 2009 11:34:25 AM CEST
 *
 * text-command parser, parses text like '<do> <this> <int|str> <to> <that>'
 * 
 * Changelog:
 *
 * 	[Tue 14 Apr 2009 11:34:25 AM CEST] 
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
 */

class parser{

  // If a format matches, results are cached in these vars
  private $matchFormat;
  private $matchStr; 

  public function __construct(){}

  /** 
   * matchFormatParam - looks if a string meets the 'type' standard.
   *                    example: matchFormatParam('1','int')               // will return true
   *                             macthFormatParam('1', array('str','int')  // will return true
   *                             macthFormatParam('bla',array('str','bla') // will return true
   * @param string $str can be anything 
   * @param string $type can be any string OR 'int' (integercheck) OR 'str' (stringcheck) OR array like ('0=>'int',2=>'str') 
   * @return bool
   */ 
  public function matchFormatParam( $str, $type ){
    $match = false;
    if( is_array($type) ){
      foreach( $type as $t )
        if( $this->matchFormatParam( $str, $t ) )
          $match = true;
    }else{
      switch( $type ){
        case "int": $match = is_numeric( $str );          break;
        case "str": $match = is_string( $str );           break;
        case "*"  : $match = true;                        break;
        default:    $match = ($type == $str);             break;
      }
    }
    return $match;
  }

  /** 
   * matchFormat - looks if a string meets a certain format 
   *               example: matchFormat( "ik ben gordon en ben 21 jaar", "[ik] [ben] [str] [en] [ben] [int] [jaar]") // returns true
   * @param string $var description 
   * @return mixed The new value 
   */ 
  public function matchFormat( $str, $format ){
    // lets divide into tokens (look for space) and make arrays if token has '|'-charachter
    $format       = str_replace( array("[","]"), "", $format );
    $partsStr     = explode(" ", $str );
    $match        = true;
    $partsFormat  = explode(" ", $format );
    foreach( $partsFormat as $k => $p )
      $partsFormat[ $k ] = strstr( $p, "|" ) ? explode("|", $p ) : $p;
    // look if format matches
    for( $i = 0; $i < count($partsStr) && $i < count($partsFormat); $i++ )
      $match     &= $this->matchFormatParam( $partsStr[$i], $partsFormat[$i] );
    // save if found
    if( $match ){
      $this->matchFormat = $partsFormat;
      $this->matchStr    = $partsStr;
    }
    return $match;
  }

  /**
   * matchFormats  - shortcut func, calls matchFormat with array of formats
   * 
   * @param mixed $str 
   * @param mixed $formatArr 
   * @access public
   * @return str with matched type 
   */
  public function matchFormats( $str, $formatArr ){
    foreach( $formatArr as $action => $format )
      if( $this->matchFormat( $str, $format ) )
        return $action;
    return false;
  }

  /** 
   * getValues - returns the dynamic values of the last Matched format 
   * 
   * @return array
   */ 
  public function getValues( )
  {
    $values = array();
    if( is_array($this->matchFormat) && is_array($this->matchStr) ){
      for( $i = 0; $i < count($this->matchFormat); $i++ ){
        $item = $this->matchFormat[$i];
        if( $item  == "int" || $item == "str" || is_array($item) )
          $values[] = ( is_numeric($this->matchStr[$i]) )? (int)$this->matchStr[$i] : $this->matchStr[$i];
        if( $item == "*" )
          $values[] = implode(" ", array_slice( $this->matchStr, $i ) );
      }
    }else return false;
    return $values;
  }

}
?>
