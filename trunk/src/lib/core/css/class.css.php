<?php
class css {
  var $css;
  var $html;
  
  function css($html = true) {
    // Register "destructor"
    register_shutdown_function(array(&$this, "finalize"));
    $this->html = ($html != false);
    $this->clear();
  }
  
  function finalize() {
    unset($this->css);
  }
  
  function clear() {
    unset($this->css);
    $this->css = array();
    if($this->html) {
      $this->add("ADDRESS", "");
      $this->add("APPLET", "");
      $this->add("AREA", "");
      $this->add("A", "text-decoration : underline; color : Blue;");
      $this->add("A:visited", "color : Purple;");
      $this->add("BASE", "");
      $this->add("BASEFONT", "");
      $this->add("BIG", "");
      $this->add("BLOCKQUOTE", "");
      $this->add("BODY", "");
      $this->add("BR", "");
      $this->add("B", "font-weight: bold;");
      $this->add("CAPTION", "");
      $this->add("CENTER", "");
      $this->add("CITE", "");
      $this->add("CODE", "");
      $this->add("DD", "");
      $this->add("DFN", "");
      $this->add("DIR", "");
      $this->add("DIV", "");
      $this->add("DL", "");
      $this->add("DT", "");
      $this->add("EM", "");
      $this->add("FONT", "");
      $this->add("FORM", "");
      $this->add("H1", "");
      $this->add("H2", "");
      $this->add("H3", "");
      $this->add("H4", "");
      $this->add("H5", "");
      $this->add("H6", "");
      $this->add("HEAD", "");
      $this->add("HR", "");
      $this->add("HTML", "");
      $this->add("IMG", "");
      $this->add("INPUT", "");
      $this->add("ISINDEX", "");
      $this->add("I", "font-style: italic;");
      $this->add("KBD", "");
      $this->add("LINK", "");
      $this->add("LI", "");
      $this->add("MAP", "");
      $this->add("MENU", "");
      $this->add("META", "");
      $this->add("OL", "");
      $this->add("OPTION", "");
      $this->add("PARAM", "");
      $this->add("PRE", "");
      $this->add("P", "");
      $this->add("SAMP", "");
      $this->add("SCRIPT", "");
      $this->add("SELECT", "");
      $this->add("SMALL", "");
      $this->add("STRIKE", "");
      $this->add("STRONG", "");
      $this->add("STYLE", "");
      $this->add("SUB", "");
      $this->add("SUP", "");
      $this->add("TABLE", "");
      $this->add("TD", "");
      $this->add("TEXTAREA", "");
      $this->add("TH", "");
      $this->add("TITLE", "");
      $this->add("TR", "");
      $this->add("TT", "");
      $this->add("UL", "");
      $this->add("U", "text-decoration : underline;");
      $this->add("VAR", "");
    }
  }
  
  function setHTML($html) {
    $this->html = ($html != false);
  }
  
  function add($key, $codestr) {
    $key = strtolower($key);
    $codestr = strtolower($codestr);
    if(!isset($this->css[$key])) {
      $this->css[$key] = array();
    }
    $codes = explode(";",$codestr);
    if(count($codes) > 0) {
      foreach($codes as $code) {
        $code = trim($code);
        try{
          list($codekey, $codevalue) = explode(":",$code);
        } catch (IOException $e) {}
        if(strlen($codekey) > 0) {
          $this->css[$key][trim($codekey)] = trim($codevalue);
        }
      }
    }
  }

  function get($key, $property, $striphash = true ) {
    $key = strtolower($key);
    $property = strtolower($property);
    
    list($tag, $subtag) = explode(":",$key);
    list($tag, $class) = explode(".",$tag);
    list($tag, $id) = explode("#",$tag);
    $result = "";
    foreach($this->css as $_tag => $value) {
      list($_tag, $_subtag) = explode(":",$_tag);
      list($_tag, $_class) = explode(".",$_tag);
      list($_tag, $_id) = explode("#",$_tag);
      
      $tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
      $subtagmatch = (strcmp($subtag, $_subtag) == 0) | (strlen($_subtag) == 0);
      $classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
      $idmatch = (strcmp($id, $_id) == 0);
      
      if($tagmatch & $subtagmatch & $classmatch & $idmatch) {
        $temp = $_tag;
        if((strlen($temp) > 0) & (strlen($_class) > 0)) {
          $temp .= ".".$_class;
        } elseif(strlen($temp) == 0) {
          $temp = ".".$_class;
        }
        if((strlen($temp) > 0) & (strlen($_subtag) > 0)) {
          $temp .= ":".$_subtag;
        } elseif(strlen($temp) == 0) {
          $temp = ":".$_subtag;
        }
        if(isset($this->css[$temp][$property])) {
          $result = $this->css[$temp][$property];
        }
      }
    }
    if( $striphash && $result[0] == '#' )
      $result = str_replace('#','',$result);
    return $result;
  }
  
  function getSection($key) {
    $key = strtolower($key);
    
    list($tag, $subtag) = explode(":",$key);
    list($tag, $class) = explode(".",$tag);
    list($tag, $id) = explode("#",$tag);
    $result = array();
    foreach($this->css as $_tag => $value) {
      list($_tag, $_subtag) = explode(":",$_tag);
      list($_tag, $_class) = explode(".",$_tag);
      list($_tag, $_id) = explode("#",$_tag);
      
      $tagmatch = (strcmp($tag, $_tag) == 0) | (strlen($_tag) == 0);
      $subtagmatch = (strcmp($subtag, $_subtag) == 0) | (strlen($_subtag) == 0);
      $classmatch = (strcmp($class, $_class) == 0) | (strlen($_class) == 0);
      $idmatch = (strcmp($id, $_id) == 0);
      
      if($tagmatch & $subtagmatch & $classmatch & $idmatch) {
        $temp = $_tag;
        if((strlen($temp) > 0) & (strlen($_class) > 0)) {
          $temp .= ".".$_class;
        } elseif(strlen($temp) == 0) {
          $temp = ".".$_class;
        }
        if((strlen($temp) > 0) & (strlen($_subtag) > 0)) {
          $temp .= ":".$_subtag;
        } elseif(strlen($temp) == 0) {
          $temp = ":".$_subtag;
        }
        foreach($this->css[$temp] as $property => $value) {
          $result[$property] = $value;
        }
      }
    }
    return $result;
  }
  
  function parseStr($str) {
    $this->clear();
    // Remove comments
    $str = preg_replace("/\/\*(.*)?\*\//Usi", "", $str);
    // parse this damn csscode
    $parts = explode("}",$str);
    if(count($parts) > 0) {
      foreach($parts as $part) {
        list($keystr,$codestr) = explode("{",$part);
        $keys = explode(",",trim($keystr));
        if(count($keys) > 0) {
          foreach($keys as $key) {
            if(strlen($key) > 0) {
              $key = str_replace("\n", "", $key);
              $key = str_replace("\\", "", $key);
              $this->add($key, trim($codestr));
            }
          }
        }
      }
    }
    //
    return (count($this->css) > 0);
  }
  
  function parse($filename) {
    $this->clear();
    if(file_exists($filename)) {
      return $this->parseStr(file_get_contents($filename));
    } else {
      return false;
    }
  }
  
  function getCSS() {
    $result = "";
    foreach($this->css as $key => $values) {
      $result .= $key." {\n";
      foreach($values as $key => $value) {
        $result .= "  $key: $value;\n";
      }
      $result .= "}\n\n";
    }
    return $result;
  }
}
?>
