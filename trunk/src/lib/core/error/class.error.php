<?
/** 
 * File:        error.php
 * Date:        Sun Dec 14 16:48:42 2008
 *
 * Error handler class
 * 
 * Changelog:
 *
 * 	[Sun Dec 14 16:48:42 2008] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   $error = sutra::get()->error;
 * </code>
 *
 */

define( E_JAVASCRIPT_ERROR,   99998 );
define( E_JAVASCRIPT_WARNING, 99999 );

class error{
 
  var $email        = array("info@leon.vankammen.eu");
  var $ignoreJS     = true;
  var $cacheUrlMax  = 3;
  var $ignoreFiles  = array("class.template.php");
  var $cssEmail     = "margin:10px;padding:10px;font-size:11px;border:1px dotted #555;background-color:#F0F0F0;font-family:Terminal,Courier;color:#555";
  var $cssBrowser   = "margin:10px;padding:10px;font-size:8px;background-color:#F0F0F0;font-family:Terminal,Courier;color:#555";
  var $html         = "";
  var $types        = array(
                          // ERROR TYPE                   SHOW  EMAIL  FATAL   LOG
                          "E_ERROR"             => array(  1,     0,     1,     1 ),
                          "E_WARNING"           => array(  1,     0,     0,     1 ),
                          "E_PARSE"             => array(  1,     0,     1,     1 ),
                          "E_NOTICE"            => array(  1,     0,     0,     1 ),
                          "E_CORE_ERROR"        => array(  1,     0,     0,     1 ),
                          "E_CORE_WARNING"      => array(  0,     0,     0,     1 ),
                          "E_COMPILE_ERROR"     => array(  0,     0,     0,     1 ),
                          "E_COMPILE_WARNING"   => array(  0,     0,     0,     1 ),
                          "E_USER_ERROR"        => array(  1,     0,     0,     1 ),
                          "E_USER_WARNING"      => array(  1,     0,     0,     1 ),
                          "E_USER_NOTICE"       => array(  1,     0,     0,     1 ),
                          "E_JAVASCRIPT_ERROR"  => array(  0,     0,     0,     1 ),
                          "E_JAVASCRIPT_ASSERT" => array(  0,     0,     0,     1 ),
                          "E_STRICT"            => array(  0,     0,     0,     0 ),
                          "E_RECOVERABLE_ERROR" => array(  0,     0,     0,     0 ),
                          "E_DEPRECATED"        => array(  0,     0,     0,     0 ),
                          "E_USER_DEPRECATED"   => array(  0,     0,     0,     0 ),
                          "E_USER_DEPRECATED"   => array(  0,     0,     0,     0 ),
                          "E_ALL"               => array(  0,     0,     0,     0 )
                      );


  public function __construct(){
    $sutra      = sutra::get();
    if( !$this->ignoreJS ){
      $sutra->tpl->inc( "lib/core/error/js/error.js" ); 
      $sutra->event->addListener( "SUTRA_URL", $this, "executeUrl" );
    }
    $sutra->tpl->inc( "/lib/core/error/js/assert.js" ); 
    // lets output error information on close
    $sutra->event->addListener( "SUTRA_CLOSE", $this, "printErrors" );
  }

  public function executeUrl( $url ){
    if( is_array( $url ) && ( in_array( "error", $url ) || in_array( "assert", $url ) ) ){
      $backtrace  = str_replace( sutra::get()->_url, "", $_POST['backtrace'] );
      unset( $_POST['backtrace'] );
      if( in_array( "error", $url ) ) 
        error::handleError(  E_JAVASCRIPT_ERROR,  $_POST['err'], $_POST['file'],  $_POST['line'], "javascript", $backtrace );
      if( in_array("assert", $url ) )
        error::handleError(  E_JAVASCRIPT_ASSERT, $_POST['err'], $_POST['file'],  $_POST['line'], "javascript", $backtrace );
    }
  }

  public static function handleError( $code, $string, $file, $line, $context, $backtrace = false ){
    $sutra    = sutra::get();
    $errtype  = "";  
    // should we ignore?
    foreach( $sutra->error->ignoreFiles as $ignoreFile )
      if( strstr( $file, $ignoreFile ) )
        return;

    switch( $code ){
      case E_ERROR:             $errtype = "E_ERROR";             break;             
      case E_WARNING:           $errtype = "E_WARNING";           break;           
      case E_PARSE:             $errtype = "E_PARSE";             break;             
      case E_NOTICE:            $errtype = "E_NOTICE";            break;            
      case E_CORE_ERROR:        $errtype = "E_CORE_ERROR";        break;        
      case E_CORE_WARNING:      $errtype = "E_CORE_WARNING";      break;      
      case E_COMPILE_ERROR:     $errtype = "E_COMPILE_ERROR";     break;     
      case E_COMPILE_WARNING:   $errtype = "E_COMPILE_WARNING";   break;   
      case E_USER_ERROR:        $errtype = "E_USER_ERROR";        break;        
      case E_USER_WARNING:      $errtype = "E_USER_WARNING";      break;      
      case E_USER_NOTICE:       $errtype = "E_USER_NOTICE";       break;       
      case E_JAVASCRIPT_ERROR:  $errtype = "E_JAVASCRIPT_ERROR";  break;       
      case E_JAVASCRIPT_ASSERT: $errtype = "E_JAVASCRIPT_ASSERT"; break;       
      case E_STRICT:            $errtype = "E_STRICT";            break;            
      case E_RECOVERABLE_ERROR: $errtype = "E_RECOVERABLE_ERROR"; break; 
      case E_DEPRECATED:        $errtype = "E_DEPRECATED";        break;        
      case E_USER_DEPRECATED:   $errtype = "E_USER_DEPRECATED";   break;   
      case E_ALL:               $errtype = "E_ALL";               break;               
    }
    $message    = "{$errtype}: '{$string}'\nat line {$line} in {$file}";
    $message    = str_replace( sutra::get()->_path, "", $message );
    $backtrace  .= $sutra->error->getBackTrace( );
    $console    = isset($sutra->cli);
    if( !$console && $sutra->error->types[ $errtype ][0] )  print( $sutra->error->generateMessage( $errtype, $message.$backtrace ) );
    if( !$console && $sutra->error->types[ $errtype ][1] )  $sutra->error->sendMail( $code.$line.$file, $message, $backtrace . $sutra->error->getBackTrace( false, true ) );
    if( !$console && $sutra->error->types[ $errtype ][2] )  $sutra->close("<PRE style='{sutra::get()->error->cssBrowser}'>" . $message.$backtrace . "</PRE>" );
    if( $sutra->error->types[ $errtype ][3] )               _log( $message.$backtrace, $errtype );
    return true;
  }

  public static function getBackTrace( $php = true, $verbose = false){
    $sutra      = sutra::get();
    $backtrace  = debug_backtrace();
    ob_start();
    if( $php ){
      print "BACKTRACE\n=========\n";
      foreach( $backtrace as $step )
        print str_pad( "{$step['function']}( ". gettype( $step['args'] )." )", 30) . "=> {$step['file']}: {$step['line']}\n";
    }
    if( $verbose ){
      print "\n\nURL\n===\n\n";
      print date( DATE_RFC822, time())." > {$_SERVER['REQUEST_URI']}";
      if( is_object($sutra->db) ){
        print "\n\n\$_QUERIES\n========\n\n";
        print_r($sutra->db->queries);
      }
      print "\n\n\$_SERVER\n========\n\n";
      print_r($_SERVER);
      print "\n\n\$_GET\n======\n\n";
      print_r($_GET);
      print "\n\n\$_POST\n======\n\n";
      print_r($_POST);
      print "\n\n\$_FILES\n=======\n\n";
      print_r($_FILES);
      print "\n\n\$_SESSION\n=========\n\n";
      print_r($_SESSION);
      print "\n\nincluded files\n==============\n\n";
      print_r( get_included_files() );
    }
    $backtrace = ob_get_contents();
    ob_end_clean();
    $backtrace = str_replace( sutra::get()->_path, "", $backtrace );
    $msg .= "\n\n";
    return $msg.str_replace(array(" called"," at"),array(" \n\tcalled"," \n\tat"), $backtrace );
  }

  /**
   * sendMail         - sends an crashreport email to admin/developer etc (Defined in $email) 
   * 
   * @access public
   * @return void
   */
  public static $emailCount = 0;
  public static $emailCache = array();
  public function sendMail( $id, $message, $backtrace ){
    if( self::$emailCount > 10 || in_array( $id, self::$emailCache ) ) return;
    $sutra  = sutra::get();
    $mail   = sutra::get()->mail;
    $mail->From = $mail->FromName  = "error@".$sutra->yaml->cfg['global']['short_domain'];
    foreach( $this->email as $email )
      $mail->AddAddress( $email );
    $mail->Subject = "SUTRA " . substr( $message, 0, 40 ) . "..";
    $mail->Body    = "<b>{$message}</b><br/><br/><PRE style='{$this->cssEmail}'>{$backtrace}</PRE>";
    $mail->AltBody = $backtrace;
    $mail->IsHTML  = true;
    $ok = $mail->Send();
    if( $mail->IsError() )
      _log( __METHOD__.": could not mail '".$mail->ErrorInfo."'", "class.error.php" );
    self::$emailCount++;
    self::$emailCache[] = $id;
  }

  function generateMessage( $errtype, $content ){
    global $errcount;
    $styleA   = "font-family:Verdana; font-size:10px;";
    $styleDiv = "{$styleA}; background-color:#F0F0F0; display:none; color:#555";
$html = <<<END
      <a href="#" style="{$styleA}" onclick='document.getElementById("{$errtype}_{$errcount}").style.display = "block"'>{$errtype}</a>
      <div id='{$errtype}_{$errcount}' style='{$styleDiv}'>
        <pre style='{$this->cssBrowser}'>{$content}</pre>
      </div>
END;
    $errcount++;
    return $html;
  }

  function printErrors(){
    //print sutra::get()->error->html;
  }
}


?>
