<?
/** 
 * File:        iface.urlListener.php
 * Date:        10-10-2009
 *
 * lib objects who implement the url listener will be notified 
 * thru the execute() function before any output has started from the cms.
 * Urlisteners can take advantage of this, for example, if you want to implement
 * some kind of special situation what to do when the url starts with 'blah/' then
 * you can create an lib object who implements the urlListener.
 *
 * Changelog:
 *
 * 	[Wed Oct  7 20:56:29 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package IZIFramework 
 */

interface urlListener{

  public function executeUrl( $url );

}

?>
