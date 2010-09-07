<?php
// load necessary files
require("class.rc5.php");

// load lib & bind to sutra engine
$sutra                  = sutra::get();
$sutra->rc5             = new RC5();

?>
