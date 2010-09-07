<?php
// load lib & bind to sutra engine
$utra                   = sutra::get();
$utra->tooltip          = 0;

// autoload javascript
$utra->tpl->inc( "/lib/core/tooltip/js/tooltip.js" );
$utra->tpl->inline( "js", "// (sutra/tooltip)\ntooltip.init(false); ajax.addCallback( tooltip.init );" );
?>
