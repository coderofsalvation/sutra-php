<?
/** 
 * File:        class.upload.php
 * Date:        Wed Mar 17 19:36:34 CET 2010
 * Author:      Leon van Kammen
 *
 * class to handle uploads in a handy way.
 * It also leaves room for file-manipulations actions (like resizing etc)
 * 
 * +---------------+     +--------------------------------------+     +----------------+
 * | HTML FORM     |---->| PHP                                  |---->| SERVER         |
 * | file id='foo' |     | addHook() // do stuff,resize etc     |     | file is stored |
 * +---------------+     | save()                               |     | at given path! |
 *                       +--------------------------------------+     +----------------+
 *                             |_____ processing hooks ___^
 * Changelog:
 *
 *  [Wed Mar 17 20:05:57 CET 2010]
 *		first sketch from scratch
 *
 * <example>
 *    HTML
 *    <input type="file" name="mypic"/>
 *
 *    PHP
 *    ===
 *    $sutra = sutra::get();
 *    $sutra->upload->addHook( "mypic",  &$this, "resize", array( 123, 234 ) );
 *    $sutra->upload->save( $_FILES, "data/upload"  );
 *    $sutra->upload->delete( array("blah", "blah_" ), "data/upload", $keep_backups  );
 *
 *    function resize( $file, $args ){ // do resizing etc }
 * </example>
 *
 * @todo description
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

 *
 */
class upload{
 
  // filesize in MB
  private $maxFileSize  = 15;
  private $hooks;
  
  function __construct(){ }

  function addHook( $id, &$object, $funcname, $args ){
    if( !is_array( $this->hooks ) )      $this->hooks = array();
    _assert( $ok = ( is_object( $object) && method_exists( $object, $funcname ) ), "upload::addProcessor() object/function '{$funcname}' does not exist :(");
    if( !isset( $this->hooks[ $id ] ) )  $this->hooks[ $id ] = array();
    if( $ok ) $this->hooks[ $id ][] = (object)array( "object" => &$object, "funcname" => $funcname, "args" => $args );
  }

  function save( $files, $dest_path, $keep_backups = false ){
    _assert( is_array( $files ) && is_dir( $dest_path ), "uploads:save() need filearray & valid dir ('{$dest_path})') as args!" );
    $ok     = true;
    $result = array();
    foreach( $files as $id => $file ){
      // save, log errors & remember filename
      $new_filename = $file['name'];
      $counter = 1;
      if( !_assert( $file['size'] < ($this->maxFileSize * ( 1024 * 1024 ) ), "filesize too big! (max {$this->maxFileSize} mb!)" ) )
        continue;
      while (is_file("{$dest_path}/{$new_filename}")) 
        $new_filename = $counter++ . "_" . $file['name'];
      $ok       &= @copy($file['tmp_name'], "{$dest_path}/{$new_filename}");
      if( $keep_backups )
        $ok     &= @copy($file['tmp_name'], "{$dest_path}/original.{$new_filename}");
      $result[]  = basename( "{$dest_path}/{$new_filename}" );
      // loop thru hooks if any
      if( isset( $this->hooks[ $id ] ) )
        foreach( $this->hooks[ $id ] as $hook )
          call_user_func( array( &$hook->object, $hook->funcname ), $new_filename, isset( $hook->args ) ? $hook->args : false );
    }
    _assert( $ok, "something went wrong while moving uploaded files..did you check the existence of path (permissions) '{$dest_path}'?");
    return $result; 
  }  
 
  function delete( $files, $dest_path, $keep_backups = false ){
    $ok = true;
    foreach( $files as $file ){ 
      $file_abs       = "{$dest_path}/{$file}";
      $file_original  = "{$dest_path}/original.{$file}";
      $valid = ( is_file( $file_abs ) && strlen($file) != 0 && strlen($dest_path) != 0 );
      if( ! ( $ok &= _assert( $valid, "upload::delete() file `{$file_abs}` not found, check path/permissions?" ) ) )
         continue;
      if( ! ( $ok &= _assert( unlink($file_abs), "file `{$file_abs}` deletion failed, check path/permissions?" ) ) )
         continue;
      if( !$keep_backups && is_file( $file_original ) )
        unlink( $file_original );
    }
    return $ok;
  }    
}
?> 
