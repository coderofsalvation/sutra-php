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
 * @license 
 *  *
 * Copyright (C) 2011, Sutra Framework < info@sutraphp.com | www.sutraphp.com >
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *

 */

class language{

  private $default;
  private $language;

  public function __construct(){
    $this->default  = sutra::get()->yaml->cfg['global']['language'];
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
