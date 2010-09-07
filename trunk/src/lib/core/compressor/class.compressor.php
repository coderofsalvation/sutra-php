<?
/** 
 * File:        class.compressor.php
 * Date:        Tue Apr 20 12:21:52 CEST 2010
 *
 * Compresses several css &.{$ext} files to single files
 * 
 * Changelog:
 *
 * 	[Tue 20 Apr 2010 12:21:29 PM CEST] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *   // some code
 * </code>
 *
 * @package sutra framework 
 */

class compressor{

  public function __construct(){}

  public function process( $files, $ext ){
    $sutra      = sutra::get();
    $hash       = "";
    $data       = "";
    _assert( is_array($files), "need array of files" );
    _assert( $ext == "js" || $ext == "css", "only .js or .css extensions are allowed" );
    foreach( $files as $file )
      $hash     .= basename( $file, ".{$ext}" )."/";
    $cacheFile  =  $sutra->_path . "/data/cache/" . md5( substr( $hash, 0, strlen($hash)-1 ) ) . ".{$ext}";
    if( !is_file( $cacheFile ) ){
      foreach( $files as $file ){
        $fileAbs = "{$sutra->_path}/{$file}";
        _assert( is_file( $fileAbs ), "'{$fileAbs}' is not a real file!" );
        $data .= ( $ext == "js" ) ? file_get_contents( $fileAbs ) : $this->compressCSS( file_get_contents( $fileAbs ), $fileAbs );
        $hash .= basename( $fileAbs, ".{$ext}" )."/";
      }
      $hash = substr( $hash, 0, strlen($hash)-1 );
      switch( $ext ){
        case "js":  $data = $sutra->JSMin->minify( $data ); break;
      }
      file_put_contents( $cacheFile, $data );
    }
    $cacheFile = str_replace( $sutra->_path, "", $cacheFile );
    switch( $ext ){
      case "js":    return '    <script type="text/javascript" src="'.$cacheFile.'"></script>'."\n"; break;
      case "css":    return '    <link rel="stylesheet" href="'.$cacheFile.'" type="text/css" media="screen, projection">'."\n"; break;  
    }
    _log("TODO: better url() conversion in class.compressor.php:compressCSS()");
  }

  private function compressCSS($string, $filename = false)
  { 
    $string = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $string);
    $string = str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),'',$string);
    $from = array('{ ',' }','; ',', ',' {','} ',': ',' ,',' ;');
    $to = array('{','}',';',',','{','}',':',',',';');
    $string = str_replace($from, $to, $string);
  
    // correct filenames
    if( $filename ){
      $sutra    = sutra::get();
      $dirname  = dirname($filename);
      $urlpath  = substr( $dirname, strlen( $sutra->_path )+1, strlen($dirname)-1);
      $prefix   = "http://{$sutra->_url}{$urlpath}/";
      if( !is_dir( $sutra->_path . $urlpath ) )
        _log("possibly corrupt path '{$sutra->_path}{$urlpath}' found in '{$filename}' while compressing css");
      $string   = str_replace(  array(  "url('",
                                        "url(\"",
                                        "url(" ),
                                array(  "url('{$prefix}",
                                        "url(\"{$prefix}",
                                        "url({$prefix}" ),
                                $string );
    }
    return $string;
  } 
 
}


?>
