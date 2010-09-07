<?php
// load necessary files
require("class.phpmailer.php");

// load lib & bind to sutra engine
$sutra                  = sutra::get();
$sutra->mail            = new PHPMailer();

?>
