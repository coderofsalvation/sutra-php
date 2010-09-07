<?php
// load necessary files
require_once("class.db.php");
require_once("class.dbObject.php" );
require_once("class.dbManager.php" );

// load lib & bind to sutra engine
$sutra                  = sutra::get();
$sutra->db              = db::getInstance();
dbObject::addDecorator( new dbManager() );
?>
