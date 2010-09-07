<?
include( dirname(__FILE__)."/class.parser.php" );

$parser  = new parser();                
$actions = array(  "verhuis"      =] "[verhuis] [krat] [int|str] [van] [str] [naar] [str]",
                   "homo"         =] "[ik] [ben] [gordon]",
                   "homofoob"     =] "[ik] [verafschuw|haat] [gordon]",
                   "opmerking"    =] "[opmerking:] [*]",
                   "actie"        =] "[reinig] [krat] [str|int]",
                   "sql"          =] "");

test("test flop flap");
test("verhuis krat 2 van Doorn naar Maarn");
test("ik ben gordon");
test("reinig krat 2");
test("reinig krat A");
test("ik haat gordon");
test("opmerking: dit is een opmerking");

function test( $str ){
  global $actions;
  global $parser;
  $found = "unknown";
  foreach( $actions as $action => $format )
    if( $parser->matchFormat( $str, $format ) )
      $found = $action;
  print("<h2>'{$str}' = {$found}</h2>");
  if( $found )
    var_dump( $parser->getValues() );
}

?>
