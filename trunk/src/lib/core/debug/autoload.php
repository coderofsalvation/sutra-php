<?php
include_once( dirname( __FILE__ ). "/class.debug.php" );

// include to sutra engine & define static function
$utra                   = sutra::get();
$utra->debug            = new debug();

/**
 * _debug     - popup which displays content of mixed variable (handy debugging)
 */
function _debug( $arg )
{
  //if( !sutra::get()->debug->isActive() )
  //  sutra::get()->debug->activate();
  ob_start();
  print_r( $arg );
  $out = ob_get_contents();
  ob_end_clean();
  $out = addslashes( $out );
  $out = str_replace("\n","\\n",$out);
  $out = str_replace("\r","",$out);
  sutra::get()->tpl->inline( "js", "say('{$out}');" );
  return $arg;
}


?>
