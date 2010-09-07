<?php

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
    //check for most popular browsers first
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
}

?>
