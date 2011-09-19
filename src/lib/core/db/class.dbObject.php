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
    foreach( dbObject::$_decorators as $k => $category )
      if( $k == dbObject::$_target->_tablename || $k == "global" )
        foreach( $category as $decorator )
          if( isset( $decorator->$var ) && $result = $decorator->$var )
            return $result;
  }

  public function __set( $var, $value ){
    dbObject::$_target = $this;
    if( $var[0] != "_" )
      $this->$var = $value;
    foreach( dbObject::$_decorators as $k => $category )
      if( $k == dbObject::$_target->_tablename || $k == "global" )
        foreach( $category as $decorator )
          $decorator->$var = $value;
  }

  public function __call( $method, $args ){
    dbObject::$_target = $this;
    $response = array();
    foreach( dbObject::$_decorators as $k => $category )
      if( $k == dbObject::$_target->_tablename || $k == "global" )
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
