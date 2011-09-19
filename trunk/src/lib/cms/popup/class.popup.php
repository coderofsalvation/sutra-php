<?
/** 
 * File:        class.popup.php
 * Date:        Sat 04 Sep 2010 04:31:56 PM CEST
 *
 * Manages a popup class. You can do ajax requests (from PHP and Javascript) and handle them in the popup.
 * 
 * Changelog:
 *
 * 	[Sat 04 Sep 2010 04:31:56 PM CEST] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @version $id$
 * @copyright 2010 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package %package%
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
 * @license AGPL
 *
 * Copyright (C) <#year#>  <#name#>
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
class popup{

  public function __construct(){ 
    $sutra = sutra::get();
    $sutra->event->addListener( "SUTRA_LOAD_USER", $this, "showBig"  );
  }


  /**
   * show                 - shows the popup from PHP.
   *                        NOTE: if there is allready a popup on the screen, and you want to 
   *                        update the content inside the popup, then call update();
   * 
   * @param mixed $content 
   * @param mixed $csshide 
   * @param mixed $small 
   * @access public
   * @return void
   */
  public function show( $content = false, $csshide = false, $small = false ){
    $sutra = sutra::get();
    if( $csshide )  $sutra->tpl->assign("csshide",        true );
    if( $small   )  $sutra->tpl->assign("small",          true );
    if( $content )  $sutra->tpl->assign("popupContent",   $content );
    $sutra->tpl->assign("popup", $sutra->tpl->fetch( "lib/cms/popup/tpl/popup.tpl" ) );
  }

  /**
   * update                 - this updates the content of the popup with the given url
   *                          NOTE: only call this function when you allready have popup on the screen.
   *                          So, for example posting a form inside the popup should eventually call this function.
   *
   * @param mixed $url 
   * @access public
   * @return void
   */
  public function update( $url ){
    $sutra->ajax->getUrl( $url, "popupContent" );
  }

  public function showBig( $args, $caller ){
    $this->show( "", true );
  }
}

?>
