<?php
include_once("sutra.class.php");
_log("sutra START!");
$sutra = sutra::get();
$sutra->init();
$sutra->tpl->process( $sutra->page );

print $sutra->page->html_head;
print $sutra->page->html_main;
print $sutra->page->html_foot;

$sutra->close();
_log("sutra CLOSE");
?>
