<?php
/** 
 * File:        class.browser.php
 * Date:        Mon Sep 19 16:40:00 2011
 *
 * detects browser properties
 * 
 * Changelog:
 *
 * 	[Mon Sep 19 16:40:00 2011] 
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
 * @copyright 2011 Coder of Salvation
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

class browser
{

  function __construct(){}

  /**
   * Get browsername and version
   * @param string user agent
   * @return string browser name and version or false if unrecognized
   * @static
   * @access public
   */
  function get( $useragent = false )
  {
    $useragent = $useragent ? $useragent : $_SERVER['HTTP_USER_AGENT'];
    $matches = '';
    // check for most popular browsers first
    //unfortunately that's ie. We also ignore opera and netscape 8
    //because they sometimes send msie agent
    if(strpos($useragent,"MSIE") !== false && strpos($useragent,"Opera") === false && strpos($useragent,"Netscape") === false)
    {
      //deal with IE
      $found = preg_match("/MSIE ([0-9]{1}\.[0-9]{1,2})/",$useragent,$matches);
      if($found)
      {
        return "Internet Explorer " . $matches[1];
      }
    }
    elseif(strpos($useragent,"Gecko"))
    {
      //deal with Gecko based

      //if firefox
      $found = preg_match("/Firefox\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Mozilla Firefox " . $matches[1];
      }

      //if Netscape (based on gecko)
      $found = preg_match("/Netscape\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Netscape " . $matches[1];
      }

      //if Safari (based on gecko)
      $found = preg_match("/Safari\/([0-9]{2,3}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Safari " . $matches[1];
      }

      //if Galeon (based on gecko)
      $found = preg_match("/Galeon\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Galeon " . $matches[1];
      }

      //if Konqueror (based on gecko)
      $found = preg_match("/Konqueror\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Konqueror " . $matches[1];
      }

      //no specific Gecko found
      //return generic Gecko
      return "Gecko based";
    }

    elseif(strpos($useragent,"Opera") !== false)
    {
      //deal with Opera
      $found = preg_match("/Opera[\/ ]([0-9]{1}\.[0-9]{1}([0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Opera " . $matches[1];
      }
    }
    elseif (strpos($useragent,"Lynx") !== false)
    {
      //deal with Lynx
      $found = preg_match("/Lynx\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Lynx " . $matches[1];
      }

    }
    elseif (strpos($useragent,"Netscape") !== false)
    {
      //NN8 with IE string
      $found = preg_match("/Netscape\/([0-9]{1}\.[0-9]{1}(\.[0-9])?)/",$useragent,$matches);
      if($found)
      {
        return "Netscape " . $matches[1];
      }
    }
    else
    {
      //unrecognized, this should be less than 1% of browsers (not counting bots like google etc)!
      return "unknown";
    }
  }

  /**
   * Get browsername and version
   * @param string user agent
   * @return string os name and version or false in unrecognized os
   * @static
   * @access public
   */
  function get_os($useragent)
  {
    $useragent = strtolower($useragent);

    //check for (aaargh) most popular first
    //winxp
    if(strpos("$useragent","windows nt 5.1") !== false)
    {
      return "Windows XP";
    }
    elseif (strpos("$useragent","windows 98") !== false)
    {
      return "Windows 98";
    }
    elseif (strpos("$useragent","windows nt 5.0") !== false)
    {
      return "Windows 2000";
    }
    elseif (strpos("$useragent","windows nt 5.2") !== false)
    {
      return "Windows 2003 server";
    }
    elseif (strpos("$useragent","windows nt 6.0") !== false)
    {
      return "Windows Vista";
    }
    elseif (strpos("$useragent","windows nt") !== false)
    {
      return "Windows NT";
    }
    elseif (strpos("$useragent","win 9x 4.90") !== false && strpos("$useragent","win me"))
    {
      return "Windows ME";
    }
    elseif (strpos("$useragent","win ce") !== false)
    {
      return "Windows CE";
    }
    elseif (strpos("$useragent","win 9x 4.90") !== false)
    {
      return "Windows ME";
    }
    elseif (strpos("$useragent","mac os x") !== false)
    {
      return "Mac OS X";
    }
    elseif (strpos("$useragent","macintosh") !== false)
    {
      return "Macintosh";
    }
    elseif (strpos("$useragent","linux") !== false)
    {
      return "Linux";
    }
    elseif (strpos("$useragent","freebsd") !== false)
    {
      return "Free BSD";
    }
    elseif (strpos("$useragent","symbian") !== false)
    {
      return "Symbian";
    }
    else
    {
      return false;
    }
  }

  /**
   * isMobile  - determine if user agent is mobile
   * 
   * @access public
   * @return void
   */
  function isMobile(){
    $isMobile = false;

    $op = isset($_SERVER['HTTP_X_OPERAMINI_PHONE']) ? strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE']) : "";
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $ac = strtolower($_SERVER['HTTP_ACCEPT']);
    $ip = $_SERVER['REMOTE_ADDR'];

    $isMobile = strpos($ac, 'application/vnd.wap.xhtml+xml') !== false
        || $op != ''
        || strpos($ua, 'sony') !== false 
        || strpos($ua, 'symbian') !== false 
        || strpos($ua, 'nokia') !== false 
        || strpos($ua, 'samsung') !== false 
        || strpos($ua, 'mobile') !== false
        || strpos($ua, 'windows ce') !== false
        || strpos($ua, 'epoc') !== false
        || strpos($ua, 'opera mini') !== false
        || strpos($ua, 'nitro') !== false
        || strpos($ua, 'j2me') !== false
        || strpos($ua, 'midp-') !== false
        || strpos($ua, 'cldc-') !== false
        || strpos($ua, 'netfront') !== false
        || strpos($ua, 'mot') !== false
        || strpos($ua, 'up.browser') !== false
        || strpos($ua, 'up.link') !== false
        || strpos($ua, 'audiovox') !== false
        || strpos($ua, 'blackberry') !== false
        || strpos($ua, 'ericsson,') !== false
        || strpos($ua, 'panasonic') !== false
        || strpos($ua, 'philips') !== false
        || strpos($ua, 'sanyo') !== false
        || strpos($ua, 'sharp') !== false
        || strpos($ua, 'sie-') !== false
        || strpos($ua, 'portalmmm') !== false
        || strpos($ua, 'blazer') !== false
        || strpos($ua, 'avantgo') !== false
        || strpos($ua, 'danger') !== false
        || strpos($ua, 'palm') !== false
        || strpos($ua, 'series60') !== false
        || strpos($ua, 'palmsource') !== false
        || strpos($ua, 'pocketpc') !== false
        || strpos($ua, 'smartphone') !== false
        || strpos($ua, 'rover') !== false
        || strpos($ua, 'ipaq') !== false
        || strpos($ua, 'au-mic,') !== false
        || strpos($ua, 'alcatel') !== false
        || strpos($ua, 'ericy') !== false
        || strpos($ua, 'up.link') !== false
        || strpos($ua, 'vodafone/') !== false
        || strpos($ua, 'wap1.') !== false
        || strpos($ua, 'wap2.') !== false;
    return $isMobile;
  }

  function isBot(){
    $isBot = false;

    $op = strtolower($_SERVER['HTTP_X_OPERAMINI_PHONE']);
    $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
    $ac = strtolower($_SERVER['HTTP_ACCEPT']);
    $ip = $_SERVER['REMOTE_ADDR'];
    $isBot =  $ip == '66.249.65.39' 
    || strpos($ua, 'googlebot') !== false 
    || strpos($ua, 'mediapartners') !== false 
    || strpos($ua, 'yahooysmcm') !== false 
    || strpos($ua, 'baiduspider') !== false
    || strpos($ua, 'msnbot') !== false
    || strpos($ua, 'slurp') !== false
    || strpos($ua, 'ask') !== false
    || strpos($ua, 'teoma') !== false
    || strpos($ua, 'spider') !== false 
    || strpos($ua, 'heritrix') !== false 
    || strpos($ua, 'attentio') !== false 
    || strpos($ua, 'twiceler') !== false 
    || strpos($ua, 'irlbot') !== false 
    || strpos($ua, 'fast crawler') !== false                        
    || strpos($ua, 'fastmobilecrawl') !== false 
    || strpos($ua, 'jumpbot') !== false
    || strpos($ua, 'googlebot-mobile') !== false
    || strpos($ua, 'yahooseeker') !== false
    || strpos($ua, 'motionbot') !== false
    || strpos($ua, 'mediobot') !== false
    || strpos($ua, 'chtml generic') !== false
    || strpos($ua, 'nokia6230i/. fast crawler') !== false;
    return $isBot;
  }

  function supportWAP(){
    return ( (strpos( strtolower($_SERVER['HTTP_ACCEPT']),'pplication/vnd.wap.xhtml+xml')>0 ) ||
             ( (isset( $_SERVER['HTTP_X_WAP_PROFILE'] ) || isset($_SERVER['HTTP_PROFILE']) ) ) );
  }

  function supportHTML(){
    return ( ( strpos( strtolower($_SERVER['HTTP_ACCEPT']),'pplication/xhtml+xml') >0 ) ||
             ( strpos( strtolower($_SERVER['HTTP_ACCEPT']),'ext/html') >0 ) );
  }

}

?>
