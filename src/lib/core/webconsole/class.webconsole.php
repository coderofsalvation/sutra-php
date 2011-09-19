<?
/** 
 * File:        <#file#>.php
 * Date:        Sun Sep 11 20:11:18 2011
 *
 * webconsole for php
 * 
 * Changelog:
 *
 * 	[Sun Sep 11 20:11:18 2011] 
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
 * @copyright 2011 Coder of Salvation
 * @author Coder of Salvation, sqz <info@leon.vankammen.eu>
 * @package sutra webconsole
 * 
 * ____ _  _ ___ ____ ____   ____ ____ ____ _  _ ____ _  _ ____ ____ _  _
 * ==== |__|  |  |--< |--|   |--- |--< |--| |\/| |=== |/\| [__] |--< |-:_
 * 
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

class webconsole {

  /**
   * inited - indicates whether webconsole scripts are included 
   *
   * @var boolean
   */
  private $inited = false;


  public function __construct(){  
    $sutra = sutra::get();
    $sutra->event->addListener( "SUTRA_URL", $this, "checkUrl" );
    $sutra->event->addListener( "SUTRA_PAGE_NOT_FOUND", $this, "checkPage" );
  }

  /** 
   * checkPage - checks if url matches "webconsole" on SUTRA_PAGE_NOT_FOUND event
   *
   * @param mixed $var description 
   * @return void
   */ 
  public function checkPage( $arg ){
    $sutra = sutra::get();
    if( $this->inited || $this->checkUrl( $sutra->url->get() ) )
      $arg = $sutra->page;
  }

  /** 
   * checkUrl - checks if url matches "webconsole"
   *
   * @param mixed $var description 
   * @return void
   */ 
  public function checkUrl( $url )
  {
    $sutra = sutra::get();
    if( is_array($url) && in_array( "webconsole", $url ) && !$this->inited ){
      //$sutra->tpl->inc( "/lib/core/webconsole/termlib/compacted/termlib_min.js");
      $sutra->tpl->inc( "/lib/core/webconsole/termlib/termlib.js");
      $sutra->tpl->inc( "/lib/core/webconsole/termlib/termlib.patch.html.js");
      $sutra->tpl->inc( "/lib/core/webconsole/cfg/webconsole.config.js");
      $sutra->tpl->inc( "/lib/core/webconsole/termlib/termlib.bbs.js");
      $sutra->tpl->inc( "/lib/core/webconsole/tpl/webconsole.css");
      $sutra->tpl->assign( "prompt", $sutra->yaml->cfg['global']['short_domain'] );
      $sutra->tpl->assign( "cmds",   $this->getCommands( dirname(__FILE__)."/cfg/commands") );
      $sutra->tpl->inline( "jsDomReady", $sutra->tpl->fetch("/lib/core/webconsole/tpl/webconsole.init.js" ) );
      $this->inited = true;
    }
    return $this->inited;
  }

  public function getCommands($dir){
    $files = array();
    if($handle = opendir($dir)){ 
        while($file = readdir($handle)){ 
            clearstatcache(); 
            if(is_file($dir.'/'.$file)) $files[] = array( "file"=> $file, "cmd"=> array_shift( explode(".",$file) ) );
        } 
        closedir($handle); 
    }else _assert( false, "could not open directory '{$dir}' to open commands");
    return $files;
  }

}


?>
