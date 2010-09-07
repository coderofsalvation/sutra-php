<?
// this is an example of a (color) patch
//
// Why patches?
// ============
// Patches are usefull if you want custom features but without
// causing conflicts in the current module version.
// If you use your modules for many clients, you want to keep your
// module's functionality as basic as possible.
// This avoids many many many risks concernin testing & QA.
//
// Rule of thumb
// =============
// if you use a patch for 3 clients, then the feature should
// be built into your module.

class myPatch{

  function save( &$args, $caller){
    // add default db column 'color' in yaml section of sql table sutra_page
    _popup("save!");
    $args->yaml['color'] = strlen( $_POST['color'] ) ? $_POST['color'] : "#123123";
  }

  function fetch( &$args, $caller ){
    $sutra = sutra::get();
    if( strstr( $args['file'], "mod/webpage/tpl/backend.tpl" ) && !isset( $args['custom']['color'] ) ){
      $sutra     = sutra::get();
      $args['custom']['color'] = "/custom/mod/webpage/tpl/examplePatch.tpl";
      $args['filter']['color'] = "color";
    }
  }
}

$sutra    = sutra::get();
$myPatch  = new myPatch();
$sutra->event->addListener( "SUTRA_TPL_FETCH", $myPatch, "fetch" );
$sutra->event->addListener( "SUTRA_PAGE_SAVE_PRE", $myPatch, "save" );
?>
