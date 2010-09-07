<?php

include_once( dirname( __FILE__)."/../../sutra.class.php" );

/** 
 * _assert - nice assertion function to check incoming and outgoing params in functions
 * 
 * @param bool $expr something to assert (has to be true!)
 * @return string $msg assertion message
 */ 
function _assert( $expr, $msg ){
  $msg = "Assertion failed!\n\n {$msg}";
  if( !$expr ){
    ob_start();
    debug_print_backtrace();
    $backtrace = ob_get_contents();
    ob_end_clean();
    $backtrace = str_replace( sutra::get()->_path, "", $backtrace );
    $msg .= "\n\n";
    //  $msg = $msg.str_replace(array(" called"," at"),array(" \n\tcalled"," \n\tat"), $backtrace );
    trigger_error( $msg, E_USER_WARNING );
  }
  return $expr;
}

/**
 * _popup     - popup which displays content of mixed variable (handy debugging)
 */
function _popup( $arg )
{
  ob_start();
  print_r( $arg );
  $out = ob_get_contents();
  ob_end_clean();
  $out = addslashes( $out );
  $out = str_replace("\n","\\n",$out);
  $out = str_replace("\r","",$out);
  print "<script type='text/javascript'> alert('".$out."'); </script>";
  return $arg;
}

/* _log - logs strings into file, and truncates filesize automatically
 *
 */
function _log( $input, $section = false, $file = false, $size = 25000 ){
  if( !$file )
    $file = sutra::get()->_path ."/data/log.txt";
  // format output
  if( is_array( $input ) || is_object( $input ) ){
    ob_start();
    print_r($input);
    $input = ob_get_contents();   
    ob_end_clean();
  }
  $input    = date( DATE_RFC822, time())." SUTRA > ". ($section ? "({$section}) " : "") . $input;
  // write line
  $f        = fopen( $file, "a+" );
  fwrite( $f, $input."\n" );
  fclose( $f );
  // cut log
  $fsize    = filesize( $file );
  if( $fsize < $size )
      return;
  $f = fopen($file, "r+");
  fseek( $f, $fsize - $size );
  $log      = '';
  fgets($f,1024); /* Skip incomplete first line */
  while( ($chunk=fread($f,4096)) )
      $log  .= $chunk;
  fseek( $f, 0 );
  ftruncate( $f, strlen($log) );
  fwrite( $f, $log );
  fclose($f);
  return $input;
}

?>
