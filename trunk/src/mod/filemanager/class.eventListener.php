<?
/** 
 * File:        <#file#>.php
 * Date:        <#date#>
 *
 * description 
 * 
 * Changelog:
 *
 * 	[Sun Aug 16 01:39:43 2009] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package Sutra Framework 
 */

class filemanagerEventListener{

  public function saveFile(){
    $sutra    = sutra::get();
		if( !_assert( $sutra->acl->isAllowed( "SUTRA_MOD_WRITE" ), "SUTRA_MOD_WRITE permission not granted!" ) )
			$sutra->ajax->getUrl( "filemanager/backend?error_add", "popupContent" );
    $path     = "{$sutra->_path}/{$sutra->mod->filemanager->cfg['system']['upload_path']}";
    $subpath  = false;
    $ok       = false;

    // check for subpath
    $input    = array_merge( $_POST, $_GET );
    if( isset( $input['path'] ) )
      if( is_dir( "{$path}/{$input['path']}" ) && is_writable( "{$path}/{$input['path']}" ) )
        $subpath = "{$path}/{$input['path']}";
    if( !strlen($subpath) && !$sutra->mod->filemanager->cfg['system']['upload_in_root'] )
      return $sutra->popup->getUrl( "filemanager/backend?add_noroot=1" );
    $ok     = $sutra->upload->save( $_FILES, $subpath ? $subpath : $path );
    print $ok ? "OK" : ":(";
    $sutra->ajax->getUrl( $ok ? "filemanager/backend?succes_add" :  "filemanager/backend?error_add", "popupContent" );
  }

}


?>
