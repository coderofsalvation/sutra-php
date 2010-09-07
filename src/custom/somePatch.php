<?
// this is an example of a patch
//
// Why patches?
// ============
// Patches are usefull if you want custom features but without
// causing conflicts in the current module version.
// If you use your modules for many clients, you want to keep your
// module's functionality as basic as possible.
// This avoids many many many risks.
//
// Rule of thumb
// =============
// if you have used a patch more then 3 times, then consider building the feature 
// into your module.

class myPatch{
  function doSomething(){ 
    _popup("I'm a basic patch!");
  }
}

sutra::get()->event->addListener( "SUTRA_READY" , new myPatch(), "doSomething" );

?>
