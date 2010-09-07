<?
/** 
 * File:        class.cache.php
 * Date:        #date#
 *
 * Class which makes caching structures/arrays/objects easy.
 * Caching is a method to improve speed.
 * Almost in every application you want to be able to use this.
 * But..it this class prevents the developer from writing many caching code.
 * 
 * Changelog:
 *
 * 	[Fri 23 Apr 2010 05:10:22 PM CEST] 
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

class cache{

  private $cache;

  // when filesize (in MB) exceeds this number, an assertion will fail (and errorclass will send mail)
  private $maxFileSize  = 0.5;

  public function __construct(){}

  /**
   * get       - gets structure from serialized file
   * 
   * @param mixed $key 
   * @param string $file 
   * @access public
   * @return void
   */
  public function get( $key, $file = "/data/cache/sutra.cache" ){
    $sutra          = sutra::get();
    $enabled        = $sutra->yaml->cfg['global']['cache'];
    if( !$enabled ) return false;
    
    $cacheFile      = $sutra->_path.$file; 
    if( !is_array($this->cache) )
      $this->cache  = array();
    if( !isset( $this->cache[ $file ] ) )
      $this->cache[ $file ] = is_file( $cacheFile ) ? unserialize( file_get_contents( $cacheFile ) ) : array();
    return is_array( $this->cache[ $file ] ) && isset( $this->cache[ $file ][ $key ] ) ? $this->cache[ $file ][ $key ] : false;
  }

  /**
   * save     - writes structure to disk (serialized)
   * 
   * @param mixed $dir 
   * @param mixed $file 
   * @access private
   * @return void
   */
  public function save( $key, $data, $file = "/data/cache/sutra.cache" ){
    $sutra      = sutra::get();
    $cacheFile  = $sutra->_path.$file;
    $dir        = dirname( $cacheFile );
    if( !is_dir( $dir ) )
      @mkdir( $dir );
    if( !is_writable( $dir ) )
      @chmod( $dir, 0777 );
    if( !is_array($this->cache) )
      $this->cache = array();
    if( !isset( $this->cache[ $file ] ) )
      $this->cache[ $file ] = array();
    $this->cache[ $file ][ $key ] = $data;
    $cacheWritten = is_writable( $dir ) && file_put_contents( $cacheFile, serialize( $this->cache[ $file ] ) );
    _assert( $cacheWritten, "could not write cachefile '{$dir}/{$file}' : permission problem?" );
    _assert( ($size = filesize( $cacheFile )) < ($this->maxFileSize * ( 1024 * 1024 ) ), "Cache file is becoming too big (" . ( $size / 1024 ) ." KB )" );
  }
}


?>
