<?php
// load necessary files
require("class.tree.php");

// load lib & bind to sutra engine
$utra                  = sutra::get();
$utra->tree            = new tree();
$utra->tpl->register_function("tree", array(&$utra->tree, "display"));
?>
