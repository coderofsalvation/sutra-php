<?
/** 
 * File:        class.language.php
 * Date:        Mon Jun  8 23:42:51 2009
 *
 * this is a basic language files which allows switching of language thru $_POST or $_GET.
 * Also caches language in session.
 * 
 * Changelog:
 *
 * 	[Mon Jun  8 23:42:51 2009] 
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

class language{

  private $default;
  private $language;

  public function __construct(){
    $this->default  = "nl";
    $this->language = false;
    $this->init();
  }

  /** 
   * get            - gets the current language
   * 
   * @return mixed language; 
   */ 

  public function get( )
  {
    if( !$this->language )
      $this->init();
    return ($_SESSION['language'] != "nl" && $_SESSION['language'] != "en" ) ? $this->default :  $_SESSION['language'];
  }

  /*
   * init           - automatically determine language, senses $_GET['language'] & $_POST['language']
   *                  and commits to $_SESSION['language']
   */
  public function init(){
    $sutra = sutra::get();
    if( isset( $_GET['language'] ) || isset($_POST['language']) ){
      $_SESSION['language'] = isset( $_GET['language'] ) ? $_GET['language'] : $_POST['language'];
      $url = $sutra->_url;
      header("Location: http://{$url}");
      // before dieing, lets take care of things
      $sutra->event->fireEvent( "SUTRA_CLOSE" );
      $sutra->close();
    }
    if( !isset( $_SESSION['language'] ) || ($_SESSION['language'] != "nl" && $_SESSION['language'] != "en") )
      $_SESSION['language'] = $this->default;
    $locale = array(  $_SESSION['language']."_".strtoupper($_SESSION['language']),
                      $_SESSION['language']."_".strtoupper($_SESSION['language']).".utf8" );
    setlocale(LC_ALL, $locale );
    $sutra->tpl->assign("language", $_SESSION['language'] );
  }
}
?>
