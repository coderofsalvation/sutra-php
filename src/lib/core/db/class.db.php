<?
/* 
 * File:        <#file#>.php
 * Date:        Sun Dec 14 16:48:42 2008
 *
 * MySQL database class
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
 *   $db = db::getInstance( // some code
 * </code>
 *
 */

class db{

  /* connection id */
  private $id;

  /* number of queries */
  public $queries;

  /*
   * Singleton
   */
  private static $instance;
  public static function getInstance(){
    $sutra  = sutra::get();
    $host   = $sutra->yaml->cfg['global']['db_host'];
    $user   = $sutra->yaml->cfg['global']['db_user'];
    $pass   = $sutra->yaml->cfg['global']['db_pass'];
    $name   = $sutra->yaml->cfg['global']['db_name'];
    return (!db::$instance instanceof self) ? db::$instance = new self( $host, $user, $pass, $name ) : db::$instance;
  }

  private function __construct( $host = false, $user = false, $password = false, $dbname = false ){
    if( !_assert( ($this->id = mysql_connect( $host, $user, $password )), "can't connect to db, check config") )
      sutra::get()->event->fire( "DB_CONNECT_ERROR" );
    if( !_assert( mysql_select_db( $dbname ), "connected to db but dbname not correct, check config") )
      sutra::get()->event->fire( "DB_SELECT_ERROR" );
    $this->queries = array();
  }

  /* 
   * getArray() - executes query and returns an array 
   * 
   * @param string $sql sql query
   * @param boolean $return_array_of_arrays outputtype 
   * @return array $result
   */ 
  function getArray ($sql, $returnDbObjects = true, $parseYaml = true ) 
  {
    $sql      = str_replace(array("`*`"),"*",$sql);
    $result   = array();
    $tables   = array();
    $rs       = mysql_query($sql);
    $this->checkErr();
    $this->queries[] = $sql;
    if( $returnDbObjects ){
      preg_match_all('/(FROM|from) [`](\w+)[`]/i', $sql, $tables);
      _assert( count( $tables ) == 3, "getArray() returnDbObjects only work with simple queries! for advanced queries dbObjects become slow!" );
    }
    if ( _assert( is_resource($rs), "'{$sql}' failed..does table exist?") && mysql_num_rows($rs) != 0){
      while ($row = $this->strip(mysql_fetch_assoc($rs))){
        if( $parseYaml && isset( $row['yaml'] ) )
            $row['yaml'] = sutra::get()->yaml->loadString( $row['yaml'] );
        if( $returnDbObjects ){
          $sqlObj             = new dbObject( count($tables) == 3 ? $tables[2][0] : false );
          $sqlObj->populate( $row );
          $result[]           = $sqlObj;
        }else $result[] = $row;
      }
    }
    return $result;
  }

  /* 
   * getObject() - executes query and returns an object 
   * 
   * @param string $sql sql query
   * @return array $result (or false if not found)
   */ 
  function getObject ($sql, $parseYaml = true ) 
  {
    $objs     = $this->getArray( $sql, true, $parseYaml );
    return  count($objs) ? array_shift( $objs ) :false;
  }

  /* 
   * saveObject() - executes query and returns an object 
   * 
   * @param string $sql sql query
   * @return array $result (or false if not found)
   */ 
  function saveObject ( $tableName, $obj, $parseYaml = true ) 
  {
    _assert( strlen($tableName), "please specify a tablename in save() or saveObject()" );
    $arr = array();
    foreach( $obj as $variable => $value )
      if( $variable[0] != "_" ) $arr[ $variable ] = $value;
    if( $parseYaml && isset( $arr['yaml'] ) && is_array( $arr['yaml'] ) )
        $obj->yaml = utf8_encode( sutra::get()->yaml->dump( $arr['yaml'] ) );
    return  ( isset($arr['id'] ) && !empty( $arr['id'] ) && $arr['id'] ) ? 
            $this->updateArray( $tableName, $arr, $arr['id'] ) :
            $this->insertArray( $tableName, $arr );
  }

  /* 
   * stripUnderScores - strips the underscores from arraykeys
   * 
   * @param array $arr the associative array
   * @return mixed The new value 
   */ 
  function stripUnderScores( $arr )
  {
    if( !is_array( $arr ) )
      return $arr;
    $newArr = array();
    foreach( $arr as $k => $v ){
      $kparts = explode( "_", $k );
      $newArr[ $kparts[ count($kparts)-1 ] ] = $this->stripUnderScores( $v );
    }
    return $newArr;
  }
  /* 
   * insertArray() - inserts array into databasedescription 
   * 
   */ 
  function insertArray($table_name, $array) 
  {
    $checked_query_fields = array();
    $checked_query_values = array();
    foreach($array as $field_name => $field_value) {
      if( strtoupper($field_value) == "TRUE" || strtoupper($field_value) == "FALSE" )
        $checked_query_values[] = $this->escapeString($field_value);
      else
        $checked_query_values[] = "'" . $this->escapeString($field_value) . "'";
      $checked_query_fields[] = "`" . $this->escapeString($field_name) . "`";
    }
    if( _assert( count($checked_query_values) && count($checked_query_fields), "insertArray sql generation failed because of empty/nonmatching field/values" ) ){
      $query_fields = implode(",", $checked_query_fields);
      $query_values = implode(",", $checked_query_values); 
      $insert_query = "INSERT INTO `{$table_name}` ({$query_fields}) VALUES($query_values)";
      $this->query($insert_query);
    }
    return true;
  }
  /* 
   * updateArray() - updates sql row 
   * 
   * @param string $var description 
   * @return mixed The new value 
   */ 
  function updateArray($table_name, $array, $id, $id_field="id") 
  {
    $query_items = array();
    foreach($array as $field_name => $field_value) {
      if( $field_name == "yaml" && is_array( $field_value ) )
        $field_value = sutra::get()->yaml->dump( $field_value );
      if( $field_name != $id_field ){
        $isBoolean    = ( strtoupper($field_value) == "TRUE" || strtoupper($field_value) == "FALSE" );
        $field_value  = $isBoolean ? strtoupper($field_value) : $field_value;
        $quote        = $isBoolean ? "" : "'";
        $query_items[] = "`" . $this->escapeString($field_name) . "` = ". "{$quote}" . $this->escapeString($field_value) . "{$quote}";
      }
    }
    $query_items = implode(",", $query_items);
    $update_query = "UPDATE `{$table_name}` SET {$query_items} WHERE `{$id_field}` = '{$id}'";
    $this->query($update_query);
    return true;
  }

  /* 
   * query - do a query 
   * 
   * @param string $sql sql query
   * @return mixed result
   */ 

  function query( $sql )
  {
    $sql = str_replace(array("`*`"),"*",$sql);
    $rs = mysql_query($sql);
    $this->checkErr();
    $this->queries[] = $sql;
    return $rs;
  }

  /* 
   * strip() - strips slashes from array opbject (recursive)
   * 
   * @param mixed $mixed array with unstripped slashes (coming from sql) 
   * @return mixed $mixed stripped var
   */ 
  function strip($mixed) 
  {
    if (is_array($mixed)) {
      foreach($mixed as $key => $value) {
        if (is_array($value) || is_object($value)) $mixed[$key] = $this->strip($value);
        else $mixed[$key] = stripslashes($value);
      }
    } elseif (is_object($mixed)) {
      $assoc_obj_vars = get_object_vars($mixed);
      foreach($assoc_obj_vars as $key => $value) {
        if (is_array($value) || is_object($value)) $mixed->$key = $this->strip($value);
        else $mixed->$key = stripslashes($value);
      } 
    } else {
      $mixed = stripslashes($mixed);
    }
    return $mixed;
  }

  /* 
   * escapeString() - recursive escapestring function
   * 
   * @param mixed $var misc. assoc array 
   * @return mixed escaped assoc array
   */ 
  function escapeString ($mixed) 
  { 
    if (is_array($mixed)) {
      foreach($mixed as $key => $value) {
        if (is_array($value) || is_object($value)) 
          $mixed[$key] = $this->escapeString($value);
        else {
          //$value = str_replace("%", "\\%", $value);
          if(version_compare(phpversion(), "4.3.0", "<") )
            $mixed[$key] = mysql_escape_string($value);
          else
            $mixed[$key] = mysql_real_escape_string($value, $this->id);
        }
      }
    } elseif (is_object($mixed)) {
      $assoc_obj_vars = get_object_vars($mixed);
      foreach($assoc_obj_vars as $key => $value) {
        if (is_array($value) || is_object($value))
          $mixed->$key = $this->escapeString($value);
        else {
          //$value = str_replace("%", "\\%", $value);
          if(version_compare(phpversion(), "4.3.0", "<") || !$this->id)
            $mixed[$key] = mysql_escape_string($value);
          else
            $mixed[$key] = mysql_real_escape_string($value, $this->id);
        }
      } 
    } else {
      //$value = str_replace("%", "\\%", $mixed);
      if(version_compare(phpversion(), "4.3.0", "<") || !$this->id)
        $mixed = mysql_escape_string($mixed);
      else
        $mixed = mysql_real_escape_string($mixed, $this->id);
    }
    return $mixed;
  }

  /* 
   * import - imports a huge sql dump
   * 
   * @param string $sqldump your sql dump (made with phpmyadmin or mysqldump)
   * @return bool succes
   */ 

  function import( $sqldump )
  {
    $sqldump = explode( "\n", $sqldump );
    $query = "";
    foreach($sqldump as $sql_line) {
     $tsl = trim($sql_line);
     if (($tsl != "") && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != "#")) {
       $query .= $sql_line;
       if(preg_match("/;\s*$/", $sql_line)) {
         $result = $this->query($query);
         if (!$result) return false;
         $query = "";
       }
     }
    }
    return true;
  }

  /* 
   * checkErr() - check for mysql errors
   * 
   */ 
  function checkErr() 
  {
    _assert( !mysql_errno($this->id), __FILE__." db (database) error: " . mysql_errno($this->id) . " - " . mysql_error($this->id));
  }
}


?>
