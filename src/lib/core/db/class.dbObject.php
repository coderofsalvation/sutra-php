<?
/** 
 * File:        class.dbObject.php
 * Date:        Sun Dec 14 16:48:42 2008
 *
 * SQL Object. Makes life easier for retrieving/storing database contents/relations.
 * It also enables the programmer to decorate sql-tables with php functions (makes cleaner code).
 *
 * Changelog:
 *
 * 	[Sun Dec 14 16:48:42 2008] 
 *		first sketch from scratch
 *
 * @todo description
 *
 * Usage example: 
 * <code>  
 *        $obj      = new dbObject( "sutra_item ");
 *        // mapping of functions
 *        class foo{ function doStuff( $args ){ print "hi!"; } }
 *        class fee{ function doStuff( $args ){ print "ho!"; } }
 *        dbObject::addDecorator( new foo() );
 *        dbObject::addDecorator( new fee(), "sutra_item" ); // only for sutra_item sql table
 *        $obj->doStuff();
 * </code>
 *
 */
class dbObject{

  /* decorator */
  public static   $_decorators  = array( "global" => array() );
  public static   $_target      = null;

  public          $_tablename;
  public static   $_created     = 0;

  public function __construct( $tablename = false ){
    $sutra = sutra::get();
    $this->_tablename     = $tablename;
    // check for any decorators in /db
    if( file_exists( $sutra->_path . "/db/{$tablename}.php") )
      include_once( $sutra->_path . "/db/{$tablename}.php" );
    // copy vars from decorator
    if( strlen($tablename) && isset( dbObject::$_decorators[ $tablename ] ) )
      foreach( dbObject::$_decorators[ $tablename ] as $decKey => $decorator )
        foreach( $decorator as $var => $v )
          if( $var != "target" ) $this->$var = $v;
    dbObject::$_created++;
  }

  public function __get( $var ){
    dbObject::$_target = $this;
    foreach( dbObject::$_decorators as $category )
			foreach( $category as $decorator )
				if( isset( $decorator->$var ) && $result = $decorator->$var )
					return $result;
  }

  public function __set( $var, $value ){
    dbObject::$_target = $this;
    if( $var[0] != "_" )
      $this->$var = $value;
    foreach( dbObject::$_decorators as $category )
			foreach( $category as $decorator )
				$decorator->$var = $value;
  }

  public function __call( $method, $args ){
    dbObject::$_target = $this;
    $response = array();
    foreach( dbObject::$_decorators as $category )
      foreach( $category as $decorator )
        if( method_exists( $decorator, $method ) )
          $response[ get_class( $decorator ) ] = call_user_func_array( array( &$decorator, $method ), $args );
    return count($response) == 1 ? array_shift($response) : $response;
  }

  public static function addDecorator( $object, $tablename = false ){
    _assert( $isObject = is_object($object), "addDecorator() need object as argument!");
    $tablename =  !$tablename ? "global" : $tablename;
    if( $isObject ){
      if( !isset( dbObject::$_decorators[ $tablename ] ) )
        dbObject::$_decorators[ $tablename ] = array(); 
      dbObject::$_decorators[ $tablename ][] = $object;
    }
  }
}
