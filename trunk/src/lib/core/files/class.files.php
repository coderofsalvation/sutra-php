<?
/** 
 * File:        files.class.php
 * Date:        Wed Dec  3 21:59:54 2008
 * Author:      Leon van Kammen
 *
 * class to get file listings or tree's
 *
 * $arr  = sutra::get()->files->getDir( dirname(__FILE__)."/data" );
 * $tree = sutra::get()->files->getTree( dirname(__FILE__)."/data" );
 *
 * Changelog:
 *
 * 	[Wed Dec  3 21:59:54 2008] 
 *		first sketch from scratch
 *
 * @todo description
 *
 */
class files{

  function __construct(){ }

  /**
   * getDir - returns array with files
   *
   * @param string $dirname name of the directory
   * @param bool   $recursive also parse subdirectories, (associative array)
   * @param array  $forbiddenExt array with forbidden extensions
   * @param string $prefix, in additional cases all files can be prefixed with a string
   * @return mixed The new value
   */
  public function getDir( $dirname, $recursive = false, $forbiddenExt = false, $dirnames = false, $prefix = false, $onlydirs = false ){
    $files   = array();
    _assert( !empty($dirname) && is_dir($dirname), "files::getDir: Directory empty or not valid! :[");
    if( !$forbiddenExt )
      $forbiddenExt = array();
    if ($dh = opendir($dirname)) {
      while (($file = readdir($dh)) !== false) {
        $ext = pathinfo( $file );
        $ext = isset($ext['extension']) ?  $ext['extension'] : "";
        $isDir = ( filetype($dirname. "/" .$file) == "dir" ) && ($file[0] != ".");
        if( ($isDir && $dirnames ) || !$isDir )
          if( ( $file[0] != '.' && !in_array( $ext, $forbiddenExt ) ) && ( ($onlydirs && $isDir ) || !$onlydirs ) )
            $files[] = $prefix.$file;
        if( $isDir && $recursive )
          $files = array_merge( $files, $this->getDir( $dirname . "/" . $file , true, $forbiddenExt, $dirnames, "{$prefix}{$file}/", $onlydirs ) );
      }
      closedir($dh);
    }
    return $files;
  }

  /**
   * getTree        - returns tree (slapped associative array) with files
   *                  NOTE: this works like a CHARM together with lib/treeManager & lib/tree
   *                        the output of this function can be passed as input for treeManager::getTree( $input )
   *                        which, in turn can make a associatie array out of it..or vice versa
   *
   * @param string $dirname name of the directory
   * @param bool   $recursive also parse subdirectories, (associative array)
   * @param array  $forbiddenExt array with forbidden extensions
   * @param string $prefix, in additional cases all files can be prefixed with a string
   * @return mixed The new value
   */
  public function getTree( $dirname, $recursive = false, $forbiddenExt = false, $dirnames = false, $prefix = false, $onlydirs = false ){
    global $id;
    if( !$id ) $id = 1;
    $files   = array();
    _assert( !empty($dirname) && is_dir($dirname), "files::getDir: Directory empty or not valid! :[");
    if( !$forbiddenExt )
      $forbiddenExt = array();
    if ($dh = opendir($dirname)) {
      while (($file = readdir($dh)) !== false) {
        $ext = pathinfo( $file );
        $ext = isset($ext['extension']) ?  $ext['extension'] : "";
        $isDir = ( filetype($dirname. "/" .$file) == "dir" ) && ($file[0] != ".");
        if( ($isDir && $dirnames ) || !$isDir ){
          if( $file[0] != '.' && !in_array( $ext, $forbiddenExt ) ){
            $parts       = explode( "/", "{$prefix}/{$file}" );
            $fileElement = array( "id"              => $id,
                                  "parent_id_name"  => (count($parts) > 1)  ? $parts[ count($parts)-2 ] : false,
                                  "parent_id"       => (count($parts) > 1 ) ? false : 0,
                                  "name"            => $dirname."/".$file, 
                                  "path"            => "{$prefix}/{$file}", 
                                  "name_short"      => $file, 
                                  "size"            => $isDir ? 0 : filesize("{$dirname}/{$file}"), 
                                  "lastmod"         => filemtime("{$dirname}/{$file}"),
                                  "weight"          => $id,
                                  "extension"       => $ext
                                );
            if( ($onlydirs && $isDir ) || !$onlydirs )
              $files[] = $fileElement;
            $id++;
          }
        }
        if( $isDir && $recursive )
          $files = array_merge( $files, $this->getTree( $dirname . "/" . $file, true, $forbiddenExt, $dirnames, "{$prefix}/{$file}", $onlydirs ) );
      }
      closedir($dh);
    }
    return $this->convertParentIds( $files );
  }

  private function convertParentIds( $files ){
    // ensure that every parent_id name is converted to an integer id
    // we have no assurance of directories getting listed BEFORE files, thats why we do it afterwards
    foreach( $files as $key => $file )
      if( !$file['parent_id'] && $file['parent_id_name'] !== false )
        foreach( $files as $_file )
          if( $file['parent_id_name'] == $_file['name_short'] )
            $files[ $key ]['parent_id'] = $_file['id'];
    return $files;
  }


}
?>
