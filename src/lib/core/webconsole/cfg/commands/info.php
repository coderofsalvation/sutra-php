<?
if( !isset($_GET['argv']) || !is_array($_GET['argv']) || count($_GET['argv']) < 2 ) 
  die("Usage: info <server|session|sutra|log>");

if( $_GET['argv'][1] == "server"  ) print_r($_SERVER);
if( $_GET['argv'][1] == "session" ) print_r($_SESSION);
if( $_GET['argv'][1] == "log"     ) print file_get_contents( dirname(__FILE__)."/../../../../../data/log.txt" );
if( $_GET['argv'][1] == "sutra"   ){
  include_once( dirname(__FILE__)."/../../../../../sutra.class.php");
  $sutra = sutra::get();
  $sutra->init(false,false);
  print_r( $sutra->yaml->cfg );
  $sutra->close();
}

?>
