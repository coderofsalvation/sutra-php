<?php
// load necessary files
require("class.spyc.php");

// load lib & bind to sutra engine
$sutra                  = sutra::get();
$sutra->yaml            = new Spyc();
$sutra->yaml->setDir( dirname(__FILE__)."/../../../data" );
?>
