<?
/** 
 * File:        class.litesite.php
 * Date:        Sat Dec 12 15:35:47 2009
 *
 * library functions to serve a simple 'lite' content managable site
 * NOTE: do not try to go beyond this functionality...better switch to the cms package.
 *
 * the site functions as followed:
 *   - the content is located in /data/content.yaml.php
 *   - normally the "home" page is loaded
 *   - else the first url argument will be taken as key index (like 'www.yoursite.com/home' = 'home' )
 *   - 
 * Changelog:
 *
 * 	[Sat Dec 12 15:35:47 2009] 
 *		first sketch from scratch
 *  [Sun Dec 13 23:49:00 2009]
 *    created menu structure in page
 *
 */

class litesite {

  private $editKey    = 'admin'; // trigger content-edit mode by typing http://www.yoursite.com/admin
  private $pageItems  = 3;

  public function __construct(){}

  public function getPage(){
    $sutra  = sutra::get();
    $url    = $sutra->url->get();
    return ( !$url || !is_array( $url ) ) ? "home" : array_shift( $url );
  }

  public function checkAdminMode(){
    $sutra = sutra::get();
    if( isset( $_GET[ $this->editKey ] ) ){
      $sutra->nicedit;
      $sutra->tpl->assign("admin", true );
      _popup("Welcome to edit mode\n\n1) Klik on a text to begin 2) Click the floppy icon to save");
      return true;
    }
    return false;
  }

  private function assertPage( $pagename, $content ){
    if( !array_key_exists( $pagename, $content ) ){
      $content[ $pagename ] = array();
      for( $i = 1; $i <= $this->pageItems; $i++ )
        $content[ $pagename ][ "content_{$i}" ] = array();
    }
    return $content;
  }

  public function checkSaveContent( $pagename, $content ){
    $sutra    = sutra::get();
    $content  = $this->assertPage( $pagename, $content );
    for( $i = 1; $i <= $this->pageItems; $i++ ){
      if( isset( $_POST[ "content_{$i}" ] ) ) {
        $id      = "content_{$i}";
        $content[ $pagename ][ $id ] = utf8_encode( stripslashes( $_POST[ $id ] ) );
        file_put_contents( "{$sutra->_path}/data/content.yaml.php", $sutra->yaml->dump( $content ) );
        die("content saved!");
      }
    }
  }

}
?>
