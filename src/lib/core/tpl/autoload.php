<?php
// load necessary files
require("class.tpl.php");

// load lib & bind to sutra engine
$sutra                  = sutra::get();
$tpl                    = new tpl();
$tpl->compile           = false;
$tpl->force_compile     = false;
$tpl->compile_check     = false;
$tpl->cache             = false;//$sutra->yaml->cfg['global']['cache'];
$tpl->compile           = false;//$sutra->yaml->cfg['global']['cache'];
$tpl->cache_lifetime    = 3600;
$tpl->config_overwrite  = true;
$tpl->strict            = true;   // ignores non-existing tags
$tpl->template_dir      = "{$sutra->_path}";
$tpl->config_dir        = "{$sutra->_path}";
$tpl->cache_dir         = "{$sutra->_path}/data/cache";
$tpl->compile_dir       = "{$sutra->_path}/data/cache";

// load config files to make vars available as {#myvar#} in templates
$tpl->config_load( "/data/language_nl.yaml" );
$tpl->config_load( "/data/cfg.yaml.php" );

// load default sutra javascript
$tpl->inc( "/lib/core/functions.js" );

$sutra->tpl             = $tpl;
?>
