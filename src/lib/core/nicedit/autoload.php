<?php
// include to sutra engine & define static function
$sutra                   = sutra::get();
$sutra->nicedit          = (object)array();
$sutra->tpl->inc("/lib/core/nicedit/js/nicEdit.js");
$sutra->tpl->inline( "js", "nicConfig.iconsPath = '/{$sutra->yaml->cfg['global']['rootdir']}/' + nicConfig.iconsPath;\n" );	

?>
