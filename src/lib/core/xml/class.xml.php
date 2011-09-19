<?
/** 
 * File:        xml.class.php
 * Date:        Wed 10 Dec 2008 10:30:38 AM CET
 *
 * class to manipulate XML data from/to arrays/objects.
 * This class comes in handy when you are not able to use PHP5's simplexml, OR need to
 * do 2way conversions between array/xml.
 *
 * NOTE : avoid using attributes in your XML. In order to keep the array<->xml conversion
 *        reversible, a choice had to been made if I use tagnames- or attributes- as array-key.
 *
 * credits : big up to my man Ahmed Magdy Ezzeldin, who created the
 *           lowlevel assoc array conversions.
 *           I've also implemented this before, but this was with PHP's simplexml
 *           extension, which I found out isn't installed on every server =]
 * 
 * Changelog:
 *
 * 	[Wed 10 Dec 2008 10:30:38 AM CET] 
 *		first sketch with Ahmed Magdy Ezzeldin's functions
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 * [XML]
 *   <test>
 *    <someKey>someValue</someKey>
 *   </test>
 * [PHP]
 *   $xmlfile  = dirname(__FILE__)."/xmlpatcher.xml";
 *   $xml      = file_get_contents( $xmlfile );
 *   
 *   print "\n[reading xml]-------------------------------------\n";
 *   print htmlspecialchars( (string)$xml );
 *   print "\n[converting to array]-----------------------------\n";
 *   print_r( $arr = $mgr->xml_to_array( $xml ) );
 *   print "\n[adding new variable]-----------------------------\n";
 *   print( $arr['hoi'][] = "added new variable!!!");
 *   $xml = $mgr->array_to_xml( $arr );
 *   print "\n[saving new xml]-----------------------------\n";
 *   $f = fopen( $xmlfile, "w+" );
 *   fwrite( $f, $xml );
 *   fclose($f);
 *   print( htmlspecialchars($xml) );
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

class xml
{

  function __construct(){}
 
  /**
   * Sets padding in xml text.
   * Helps to make the xmlcode readable but can be disabled by emptying arg_str_pad when we do not need to read the xml code.
   * @param arg_int_pad_number the number of indentation pads in this tag.
   * @param arg_str_pad the single pad size.
   * @return String pad.
   * @access Private.
   */
  private function pad($arg_int_pad_number=0, $arg_str_pad="")
  {
    if(($arg_int_pad_number===0) || ($arg_str_pad===""))return "";
    $i = 0;
    $str_pad = "";
    while($i < $arg_int_pad_number){
      $str_pad .= $arg_str_pad;
      $i++;
    }
    return ($str_pad ? "\n" . $str_pad : "");
  } 

  /**
   * Private Array to XML.
   * changes php arrays into xml text recursively.
   * @param arg_arr_array the array to be changed into XML.
   * @param arg_int_pad_number the number of pads of the current tag.
   * @param arg_str_pad the indentation pad text.
   * @return String xml text.
   * @see array_to_xml().
   * @access Private.
   */
  private function prv_array_to_xml($arg_arr_array, $arg_int_pad_number=0, $arg_str_pad="")
  {
    $str_xml = "";
    while(list($k, $v) = each($arg_arr_array)){
      $tagname = ( is_int($k) ) ? "element" : htmlspecialchars($k);
      $str_xml .= $this->pad($arg_int_pad_number, $arg_str_pad) . "<" . $tagname . ">";
      if(is_array($v)){
        $str_xml .= $this->prv_array_to_xml($v, $arg_int_pad_number+1, $arg_str_pad);
      }else{
        $str_xml .= $this->pad($arg_int_pad_number+1, $arg_str_pad) . htmlspecialchars($v);
      }
      $str_xml .= $this->pad($arg_int_pad_number, $arg_str_pad) . "</". $tagname . ">";
    }
    return $str_xml;
  }

 /**
  *  Private XML to Array.
  *  Converts xml to array recursively.
  *  @param arg_tags the raw array of tags got from xml_parse_into_struct is passed by reference to keep the position of the pointer in the array through function calls.
  *  @param arg_current_tag the current array to be filled is passed by reference because it is changed within the function.
  *  @return Array.
  *  @access Private.
  */
  private function prv_xml_to_array(&$arg_tags, &$arg_current_tag = false)
  {
    while( list(,$arr_tag) = each($arg_tags) )
    {
      if($arr_tag['level'] > 1)
      {
        if ($arr_tag['type']=="complete")     // if type = complete
        { 
          //$arg_current_tag[$arr_tag['attributes']['KEY']] = $arr_tag['value'];
          if( !is_array( $arg_current_tag[ strtolower($arr_tag['tag']) ] ) )
            $arg_current_tag[ strtolower($arr_tag['tag']) ] = $arr_tag['value'];
        } 
        elseif ($arr_tag['type']=="open")   // if type = open
        {
          $arr_tag['tag'] = strtolower( $arr_tag['tag'] );
          $this->prv_xml_to_array($arg_tags, $arg_current_tag[$arr_tag['tag']]);
        } 
        elseif ($arr_tag['type']=="close")    // if type = close
        {
          return;
        }
      }
    }
  }
  
  /**
   * XML to Array.
   * Converts the XML text that was generated by this class to an array.
   * It can work with unidimensional and multidimensional associative arrays.
   * @param arg_str_xml The xml text to be changed into an array.
   * @return Array.
   * @see prv_xml_to_array().
   * @access Public.
  */
  public function xml_to_array($arg_str_xml)
  {
    $arg_str_xml   = str_replace( array(" ","\n","\t","\r"),"", $arg_str_xml  );
    $parser = xml_parser_create();
    if( xml_parse_into_struct($parser, $arg_str_xml, $arr_raw_xml, $arr_raw_index ) != 1 )
      trigger_error( "sutra->xml->xml_to_array(): malformed xml: \n\n{$arg_str_xml}", E_USER_NOTICE );
    $arr_out = array();
    $this->prv_xml_to_array($arr_raw_xml, $arr_out);
    xml_parser_free( $parser );
    return $arr_out;
  } 

  /** 
   * Array to XML.
   * changes php arrays into xml text recursively.
   * @param arg_arr_array the array to be changed into XML.
   * @param arg_str_operation_name the name of the main xml tag.
   * @param arg_str_pad the indentation pad text.
   * @return String xml text.
   * @see prv_array_to_xml().
   * @access Public.
   */
  public function array_to_xml($arg_arr_array, $arg_str_operation_name="response", $arg_str_pad="")
  {
    if(!is_array($arg_arr_array))return false;
    $str_xml = "<$arg_str_operation_name>\n";
    $str_xml .= $this->prv_array_to_xml($arg_arr_array, 1, $arg_str_pad);
    $str_xml .= ($arg_str_pad==="" ? "" : "\n") . "</$arg_str_operation_name>\n";
    return $str_xml;
  } 
}
?>
