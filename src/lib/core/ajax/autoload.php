<?php
// load necessary files
require("class.ajax.php");

// load lib & bind to sutra engine
$sutra                   = sutra::get();
$sutra->ajax             = new ajax();
$sutra->tpl->register_block("ajax", array(&$sutra->ajax, "isolate"));
$sutra->tpl->inc("/lib/core/ajax/js/ajax.js");
$sutra->tpl->inc("/lib/core/ajax/js/fade.js");
$sutra->tpl->inline("js", "// (sutra/ajax)\najax.init( SESSION_ID, baseurl );\n" );
?>
