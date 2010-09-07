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
 * @license Coder Of Salvation Supported License
 *
 * CODER OF SALVATION SUPPORTED LICENSE
 * ===============================================================================
 * 
 * Coder Des Heils Licence Agreement Please READ this Coder Des Heils
 * Supported Version licence agreement (SSV) carefully before DOWNLOADING,
 * INSTALLING or USING the Software.
 * 
 * The terms of this Licence constitute an agreement between you, either an
 * individual or a company or similar entity, as the purchaser or user of the
 * Software (hereafter "You") and Coder Des Heils (hereafter "Me"). This Licence 
 * applies to the Software, Documentation and any updates which have been provided 
 * for the Software. This is not the complete license, but here is the main thought:
 * 
 * I WANT TO CONTRIBUTE TO THE OPENSOURCE COMMUNITY AS LONG AS IT DOES NOT HARM YOU
 * OR YOUR CORE BUSINESSFIELD.  YOU SHOULD ALWAYS  APPROACH ME FOR FURTHER
 * IMPLEMENTATIONS, EXTENSIONS, MODULES, CUSTOM PATCHES BEFORE APPROACHING OTHERS.
 * %license%
 */

class translate {

  public function __construct(){  }

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
