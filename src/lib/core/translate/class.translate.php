<?
/** 
 * File:        class.translate.php
 * Date:        Mon 06 Sep 2010 05:16:00 PM CEST
 *
 * handy class to translate texts to another lanuage by using Google's translate
 * 
 * Changelog:
 *
 * 	[Mon 06 Sep 2010 05:16:00 PM CEST] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @version $id$
 * @copyright 2010 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
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

class translate {

  public function __construct(){  }

  public function processHTML( $html, $languagecode_from, $languagecode_to, $forbiddenClasses = array(), $tags = array("a","b","span","h1","h2","h3","h4","h5","h6","i","div","td","p","li") ){
    $dom = new DOMDocument();
    $dom->loadHTML( $html );
    foreach ($tags as $tag ){
      $elements = $dom->getElementsByTagName( $tag );
      foreach( $elements as $value ){
        if( !in_array( $value->attributes->getNamedItem("class")->nodeValue, $forbiddenClasses ) &&
            !in_array( $value->attributes->getNamedItem("id")->nodeValue,    $forbiddenIds ) )
//          $text = $value->nodeValue;
//          $text = str_replace( "<br/>", "<br>", $value->nodeValue );
//          $text = str_replace( "<br>", "'br'", $value->nodeValue );
//          $text = $this->process( $text, $languagecode_from, $languagecode_to );
//          $text = str_replace( "'br'", "<br>", $text );
          $value->nodeValue = $this->process( $value->nodeValue, $languagecode_from, $languagecode_to );
          //$value->nodeValue = str_replace( "'br'", "<br>", $value->nodeValue );
      }
    }
    return $dom->saveHTML();
  }

  public function process( $text, $languagecode_from, $languagecode_to ){
    $sutra = sutra::get();
    // Basic request parameters:
    // s = source language
    // d = destination language
    // q = Text to be translated
     
    $s = $languagecode_from;
    $d = $languagecode_to;
    $lang_pair = urlencode($s.'|'.$d);
    $q = urlencode($text);
     
    // Google's API translator URL
    $url = "http://ajax.googleapis.com/ajax/services/language/translate?v=1.0&q=".$q."&langpair=".$lang_pair;
     
    // Make sure to set CURLOPT_REFERER because Google doesn't like if you leave the referrer out
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, "http://{$sutra->_url}");
    $body = curl_exec($ch);
    curl_close($ch);
     
    $json = $sutra->json->decode($body);
    return $json->responseData->translatedText;
  }

}


?>
