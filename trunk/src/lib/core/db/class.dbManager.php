<?
/** 
 * @file:        class.dbManager.php
 * Date:        Sun Dec 14 16:48:42 2008
 *
 * dbObject decorator class with database manager functionality.
 * The functions in here will be shortcuts for often performed database functionality.
 * Also it makes automapping of database relations, and php functions  possible.
 * Look at this example :
 * 
 *        +-[ sutra_item ]------------------------+     +-[ sutra_category ]--+
 *  SQL   | id  | name  | category_id | parent_id |     | id  |  name         |
 *        +---------------------------------------+     +---------------------+
 *
 *  PHP   automapping of relations
 *
 *        // dbObject::addDecorator( new dbManager() ); <- since its a core decorator this is not needed
 *        $obj      = new dbObject( "sutra_item ");
 *        $category = $obj->category_id_rel;
 *        $parent   = $obj->parent_id_rel;
 *        
 *        // easy populating & saving
 *        $obj->populate( $_POST );                       
 *        $obj->save();
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
 * </code>
 *
 */
class dbManager{

  private static $relations = array();

  /*
   * populate               - populates this object with values from array.
   *                          This way you can easily feed html formarrays to this class.
   * @param mixed $values 
   * @access public
   * @return void
   */
  public function populate( $values, $stripPrefix = "", $urldecode = true ){
    foreach( $values as $key => $item ){
      if( $stripPrefix != "" )
        $key = str_replace( $stripPrefix, "", $key );
      dbObject::$_target->$key = $urldecode && is_string($item) ? urldecode($item) : $item;
      $this->$key = $item; // enable decorators __set() function to respond
    }
    return dbObject::$_target;
  }

  /*
   * copyTo 
   * 
   * @param mixed $target 
   * @access public
   * @return void
   */
  public function copyTo( &$target ){
    foreach( dbObject::$_target as $var => $value )
      $target->$var = $value;
  }

  /*
   * save                   - @brief save's the object
   * 
   * @param mixed $tablename 
   * @access public
   * @return void
   */
  public function save( $tablename = false ){
    dbObject::$_target->_tablename = $tablename ? $tablename : dbObject::$_target->_tablename;
    return sutra::get()->db->saveObject( dbObject::$_target->_tablename, dbObject::$_target, true );
  }

  /*
   * delete                   - @brief delete's the object
   * 
   * @param mixed $tablename 
   * @access public
   * @return void
   */
  public function delete( $tablename = false ){
    $this->_tablename = $tablename ? $tablename : $this->_tablename;
    return sutra::get()->db->query( "DELETE FROM `".dbObject::$_target->_tablename."` WHERE `id` = '".dbObject::$_target->id."'" );
  }

  public function __get( $var ){
    return $this->getRelation( $var ); 
  }

  /*
   * getRelation            - this mechanism automatically retrieves relations (works with __get()).
   *                          For instance, ifyou have a variable/column 'item_category_id' which 
   *                          points to another sqltable. You can automatically retrieve this relation 
   *                          by calling 'item_category_id_rel'. This will fire the query for you.
   *                          ..or if you have a column called 'parent_id' it will search its parent
   *                          in the same sqltable for you. Check the example in the classheader.
   *
   * @param mixed $var        
   * @access public
   * @return void
   */
  public function getRelation( $var ){
    $relation     = explode( "_", $var );
    $result       = false;
    $mapped       = isset( dbManager::$relations[ dbObject::$_target->_tablename ] ) &&
                    isset( dbManager::$relations[ dbObject::$_target->_tablename ][ $var ] );
    $externLink   = ($relation[ count($relation)-1 ]  == "rel" );
    $internLink   = ($relation[ 0 ]                   == "parent" );

    if( !$externLink && !$internLink && !$mapped )
      return false;

    // check type of relation
    if( $mapped )                 $result = $this->getRelationByMapping( $var );
    if( count( $relation ) >= 3 ) $result = $this->getRelationByKeyword( $relation, $internLink );
    return $result;
  }

  /*
   * getRelationByMapping       - gets a relation by mapping defined by calling for example:
   *
   *                              dbManager::addRelation( "items", "sutra_item_category.id", "sutra_item.item_category_id" );
   * @access private
   * @return void
   */
  private function getRelationByMapping( $var ){
    $mapping      = dbManager::$relations[ dbObject::$_target->_tablename ][ $var ]; 
    $value        = $mapping['columnFrom'];
    $value        = dbObject::$_target->$value;
    $sql          = "SELECT * FROM `{$mapping['tableTo']}` WHERE `{$mapping['columnTo']}` = '{$value}'";
    return sutra::get()->db->getArray( $sql, true, false );
  }

  /*
   * getRelationByKeyword       - gets a relation by observing if last token of a sql columnname
   *                              for example: columnname 'item_category_id_rel' indicates a relation to sql table 'item_category'
   *                              for example: columnname 'parent_id' indicates a relation to the same sql table
   * @param mixed $relation 
   * @param mixed $internLink 
   * @access private
   * @return void
   */
  private function getRelationByKeyword( $relation, $internLink ){
    array_pop( $relation );
    $columnfull   = implode( "_", $relation );
    $columnlast   = array_pop( $relation );
    $columnshort  = implode( "_", $relation );
    $table        = $internLink ? dbObject::$_target->_tablename : $columnshort;
    $table        = str_replace( "sutra_", "", $table );
    $value        = $internLink ? $columnfull : "{$columnshort}_{$columnlast}";
    $value        = dbObject::$_target->$value;
    $sql          = "SELECT * FROM `sutra_{$table}` WHERE `{$columnlast}` = '{$value}'";
    $result       = sutra::get()->db->getObject( $sql );
    return $result;
  }

  /*
   * addRelation        - automatically maps a variable to a SQL relation
   * 
   *                      example:  dbObject::addRelation( "items", "sutra_category.id", "sutra_item.item_category_id" );
   *                                $items = $yourcategoryObj->items;
   * @param mixed $column 
   * @param mixed $value 
   * @access public
   * @return void
   */
  public static function addRelation( $newcolumn, $relationFrom, $relationTo ){
    $relationF  = explode( ".", $relationFrom );
    $relationT  = explode( ".", $relationTo );
    $tableFrom  = $relationF[0];
    $tableTo    = $relationT[0];
    $columnFrom = $relationF[1];
    $columnTo   = $relationT[1];
    if( !isset( dbManager::$relations[ $tableFrom ] ) )
      dbManager::$relations[ $tableFrom ] = array();
    dbManager::$relations[ $tableFrom ][ $newcolumn ] = array (  "tableFrom"  => $tableFrom,
                                                                 "columnFrom" => $columnFrom,
                                                                 "tableTo"    => $tableTo,
                                                                 "columnTo"   => $columnTo );
  }
  public function load( $column , $value, $parseYaml = true ){
    $value  = sutra::get()->db->escapeString( $value );
    $target = dbObject::$_target;
    $out = sutra::get()->db->getArray(  "SELECT * FROM `{$target->_tablename}` WHERE `{$column}` = '{$value}' LIMIT 1", false, $parseYaml );
    if( count($out) ) $this->populate( (array)$out[0] );
    return dbObject::$_target;
  }

  public function loadAll( $returnObjects = true ){
    $target = dbObject::$_target;
    $out = sutra::get()->db->getArray(  "SELECT * FROM `{$target->_tablename}`", $returnObjects );
    return $out;
  }

  public function countAll(){
    $target = dbObject::$_target;
    $out = sutra::get()->db->getArray(  "SELECT COUNT(*) FROM `{$target->_tablename}`", false );
    return (int)$out[0]['COUNT(*)'];
  }

  /*
   * loadByProperty                 - this is just a little wrapper function for performing a sql query.
   *                                  It generates a query for you, so the code gets tighter.
   * @param mixed $column 
   * @param mixed $value 
   * @param mixed $orderBy 
   * @param string $orderDirection 
   * @param mixed $amount 
   * @param mixed $offset 
   * @access public
   * @return void
   */
  public function loadByProperty( $column, $value, $orderBy = false, $orderDirection = "ASC", $amount = false, $offset = false, $returnObjs = true, $parseYaml = true ){
    _assert( !($offset === false && $amount ) , "loadByColumn() please use \$amount AND \$offset" );
    _assert( strlen( dbObject::$_target->_tablename ) > 0, "loadByProperty() no tablename specified in \$this->_tablename" );
    $values     = array('column', 'value','orderBy','orderDirection','amount','offset');
    foreach( $values as $v ) if( $$v ) sutra::get()->db->escapeString( $$v );
    $value      = sutra::get()->db->escapeString( $value );
    $target     = dbObject::$_target;
    $where      = "";
    if( $column && $value && $value != "*" )
      $where    = strstr( $value, "%" ) ? "WHERE `{$column}` LIKE '{$value}'" : "WHERE `{$column}` = '{$value}'";
    $limit      = ( $offset !== false && $amount )  ? "LIMIT {$offset}, {$amount}" : "";
    $order      = ( $orderBy )            ? "ORDER BY `{$orderBy}` {$orderDirection}" : "";
    return sutra::get()->db->getArray( "SELECT * FROM `{$target->_tablename}` {$where} {$order} {$limit}", $returnObjs, $parseYaml );
  }

  /**
   * getLastId 
   * get last id from the current database table
   * @access public
   * @return void
   */
  public function getLastId(){
    $target     = dbObject::$_target;
		$sql = "SELECT MAX(ID) AS LastID FROM `{$target->_tablename}`";
		$result = sutra::get()->db->getArray( $sql, false );
		return is_array($result) && count($result) ? $result[0]['LastID'] : -1;
  }
}
?>
