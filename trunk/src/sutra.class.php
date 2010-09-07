<?
/** 
 * File:        sutra.class.php
 * Date:        $Date$
 * Author:      Leon van Kammen
 * Revision:    $Rev$ (--preset=cms)
 *
 * framework class to bundle all library functionality and keep things tidy.
 *
 * Changelog:
 *
 * 	[Wed Dec  3 21:59:54 2008] 
 *		first sketch from scratch
 *
 * @todo description
 * @package sutra framework
 */

include_once( dirname(__FILE__)."/lib/core/functions.php");

class sutra{
 
  public    $_path;
  public    $_url;
  public    $_done    = false;
  private   $_libdirs = array( "lib/core" ); // don't edit here, but in /data/cfg.yaml.php (libdirs)

  /*
   * Singleton      - get the sutra engine everywhere by calling sutra::get()
   */
  private static $instance;
  public static function get($config=false) {
    return (!sutra::$instance instanceof self) ? sutra::$instance = new self($config) : sutra::$instance;
  }

  private function __construct(){
    $this->_path         = dirname(__FILE__);
  }

  /*
   * __get          - automatically create/init lib objects by calling sutra::get()->yourlib (autoloader)
   *                  it will look for files /lib/{package}/yourlib/autoload.php or
   *                  create a new instance of  /lib/{package}/yourlib/class.yourlib.php
   *                  Also, if the module lib (cms package) is installed, call module by sutra::get()->mod->yourmodule
   * 
   * @param mixed $var 
   * @access public
   * @return void
   */
  public function __get( $var ){
    foreach( $this->_libdirs as $dir ){
      if( is_dir( $inc = "{$this->_path}/{$dir}/{$var}" ) ){
        if( is_file( "{$inc}/autoload.php" ) ){
          include_once( "{$inc}/autoload.php" );
        }else if( is_file( "{$inc}/class.{$var}.php" ) ){
          include_once( "{$inc}/class.{$var}.php" );
          $this->$var = new $var();
        }
        return $this->$var;
      }
    }
  }

  /*
   * init()         - inits the engine & libraries
   *                  NOTE: The 'auto init' libs are a very important part which decides how to handle each request.
   *                        Basically they are 'special' libs, because they implement the lib/cms/iface.urlListener.php interface.
   *                        Each library's constructor, which implements this,  can decide to prevent further code being processed.
   *                        So, the order of loading libs should be considered with care.
   *                        There are pros/cons for this 'routing' method, but there will be no problems if know what your doing :)
   */
  function init(){
    $this->event->fire( "SUTRA_INIT" );
    // init generic stuff
    set_error_handler( array( $this->error, "handleError" ) );
    error_reporting ( E_ALL ^ E_NOTICE );
    date_default_timezone_set("Europe/Amsterdam");
    // set url, so the smarty filter can correct wrong urls, and enable relative paths
    $this->_url     = $_SERVER['HTTP_HOST']."/".$this->yaml->cfg['global']['rootdir']."/";
    $libs           = $this->yaml->cfg['libs']['autocreate'];
    $this->_libdirs = array_merge( $this->_libdirs, $this->yaml->cfg['libs']['libdirs'] );
    // auto init libraries 
    foreach( $libs as $lib )
      if( !$this->done )$this->$lib;
    $this->event->fire( "SUTRA_INIT_LIBS" );
    $this->event->fire( "SUTRA_INIT_MODULES" );
    // lets give libs a chance who want to react to certains url
    if( !$this->done ) $this->event->fire( "SUTRA_URL", $this->url ? $this->url->get() : "please install url lib!" );
    // notify listening modules with init event (most likely the page module)
    if( !$this->done ) $this->event->fire( "SUTRA_READY" );
  }

  /*
   * close          - closes the engine
   */
  function close( $msg = false ){
    // notify listening modules with close event
    $this->event->fire( "SUTRA_CLOSE" );
    $this->done = true;
    if( !isset( $this->cli ) ) die( $msg );
  }

}
  
?>
