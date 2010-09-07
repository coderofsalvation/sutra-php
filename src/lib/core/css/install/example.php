<?php
include( dirname(__FILE__)."/../../../../sutra.class.php");
$utra = sutra::get();
$utra->init();

$css  = $utra->css;
$css->ParseStr("b {font-weight: bold; color: #777777;} b.test{text-decoration: underline;}");
echo $css->get("b","color");     // returns #777777
echo $css->Get("b.test","color");// returns #777777
echo $css->Get(".test","color"); // returns an empty string

$utra->close();
?>
