<?php

const MASTERTABLE    = 'sqlite_master';
const SEQUENCETABLE  = 'sqlite_sequence';
const PAGESTABLE     = 'pages';

class SQLITE extends SQLite3 { 

  private $protectedTables = array(
    MASTERTABLE,
    SEQUENCETABLE,
    PAGESTABLE
  );  

  private function queryPreparation($preparing, $values) {  
    $i = 1;
    foreach ( $values as $value )
    {
        $preparing->bindValue($i, $value);
        $i++;
    }
    return $preparing;
  }  

  public function emptyDatabase( $exclude = array() )
  {
    $forbiddenTables = $this->protectedTables;
    $forbiddenTables = array_merge($exclude, $forbiddenTables);
    $tables = $this->select(MASTERTABLE, array('name'), array('type' => 'table'));
    for ( $i = 0; $i < count($tables); $i++ )
    {
      if ( !in_array($tables[$i]['name'], $this->protectedTables) )
      {
        parent::query("DELETE FROM {$tables[$i]['name']}");
      }
    }
  }

  public function __construct( $filename )
  {
    $this->open($filename);
  }

  public function hasTable( $tablename )
  {
    return $this->query('SELECT * FROM ' . MASTERTABLE . ' WHERE type="table" and name="' . $tablename . '"')->fetchArray();
  }

  public function amount( $table, $desired, $where = false )
  {
    if ( count($desired) !== 1 || !is_string($table) || !is_array($desired) || ( !$this->hasTable($table) && $table !== MASTERTABLE ) ){
      return false;
    }
    $condition = $this->buildUpdateCondition($where)['condition'];
    $sql = "SELECT COUNT (" . $desired[0] . ") FROM '{$table}'";
    $sql .= ( $where === false ) ? '' : " WHERE " . implode(' AND ', $condition) ;

    if ( $where !== false ) {
        $preparing = $this->queryPreparation(parent::prepare($sql), $this->buildUpdateCondition($where)['values']);
    }
    else {
        $preparing = parent::prepare($sql);
    } 

    $result = $preparing->execute();
    while($row = $result->fetchArray(SQLITE3_ASSOC)) {
        return array_values($row)[0]; 
    }
     
    return 0;  
  }

  public function exists( $table, $desired, $where = false ) {
    $amount = $this->amount( $table, $desired, $where );
    return ($amount > 0);
  }

  public function selectAll($table, $where = false)
  {
    if ( !is_string($table) || ( !$this->hasTable($table) && $table !== MASTERTABLE ) ){
      return false;
    }
    $sql = "SELECT * FROM '{$table}'";
    $condition = $this->buildUpdateCondition($where)['condition'];
    $sql .= ( $where === false ) ? '' : " WHERE " . implode(' AND ', $condition) ;  

    if ( $where !== false ) {
        $preparing = $this->queryPreparation(parent::prepare($sql), $this->buildUpdateCondition($where)['values']);
    }
    else {
        $preparing = parent::prepare($sql);
    } 

    $result = $preparing->execute();
    while($row = $result->fetchArray(SQLITE3_ASSOC)) {
      $resultData[] = $row;
	}
    return $resultData;
  }

  public function select( $table, $desired, $where = false )
  {
    if ( !is_string($table) || !is_array($desired) || ( !$this->hasTable($table) && $table !== MASTERTABLE ) ){
      return false;
    }
    $sql = "SELECT " . implode(",", $desired) . " FROM '{$table}'";
    $condition = $this->buildUpdateCondition($where)['condition'];
    $sql .= ( $where === false ) ? '' : " WHERE " . implode(' AND ', $condition) ;

    if ( $where !== false ) { 
        $preparing = $this->queryPreparation(parent::prepare($sql), $this->buildUpdateCondition($where)['values']);
    } 
    else {
        $preparing = parent::prepare($sql);
    } 

    $result = $preparing->execute();
    $resultData = array();
    while($row = $result->fetchArray(SQLITE3_ASSOC))
    {
      $tmp = array();
      foreach( $row as $key => $value ) {
        if ( in_array($key, $desired, true) ) {
          $tmp[$key] = $value;
        }
      }
      $resultData[] = $tmp;
    }
    return $resultData;
  }

  public function insert( $table, $data )
  {
    if (is_object($data)) $data = (array)$data;
    if ( !is_string($table) || !is_array($data) || !$this->hasTable($table) ){
      throw new Exception("SQLITE: Invalid parameter.");
    }
    $data = $this->filterColumns($table, $data);
    $fields = array_keys($data);
    $condition = $this->buildInsertCondition($data)['condition'];
    $sql = "INSERT INTO '{$table}' ('" . implode("','", $fields) . "') VALUES (" . implode(",", $condition) . ")";
 
    $preparing = $this->queryPreparation(parent::prepare($sql), $this->buildInsertCondition($data)['values']); 

	$preparing->execute(); 

    return parent::lastInsertRowid();
  }

  public function remove( $table, $where )
  {
    if ( !is_string($table) || !is_array($where) || count($where) === 0 )
    {
      return false;
    }
    $condition = $this->buildUpdateCondition($where)['condition'];
    $sql = "DELETE FROM '{$table}' WHERE " . implode(' AND ', $condition);

    $preparing = $this->queryPreparation(parent::prepare($sql), $this->buildUpdateCondition($where)['values']);

    return ( $preparing->execute() ) ? true : false ;
  }

  public function update( $table, $data, $where )
  {
    if (is_object($data)) $data = (array)$data;
    if ( !is_string($table) || !is_array($data) || !is_array($where) ) return false;
    $data = $this->filterColumns($table, $data);

    $condition = $this->buildUpdateCondition($where)['condition'];
    $dataCondition = $this->buildUpdateCondition($data)['condition'];

    $sql = "UPDATE '{$table}' SET " . implode(', ', $dataCondition) . " WHERE " . implode(' AND ', $condition); 
    $preparing = $this->queryPreparation(parent::prepare($sql), array_merge($this->buildUpdateCondition($data)['values'], $this->buildUpdateCondition($where)['values']));  
	$preparing->execute();
 
    return true;
  }

  private function buildInsertCondition( $data )
  {
    if ( $data === false ) return; 
    $preparedData = $data;
    $values = array();
    foreach ( $preparedData as $value )
    {
         $condition[] = "?";
         $values[] = $value;
    }
    return array('condition' => $condition, 'values' => $values);
  }

  private function buildUpdateCondition( $data )
  {
    if ( $data === false ) return; 
    $preparedData = $data;
    $values = array(); 

    foreach ( $preparedData as $key => $value )
    { 
         $condition[] = "`{$key}` = ?";
         $values[] = $value;
    }
    return array('condition' => $condition, 'values' => $values);
  }
  
  private function filterColumns( $table_name, $data ) {
    $tablesquery = parent::query(sprintf("PRAGMA table_info(%s);", $table_name));
    $column_names = array();
    while ($column = $tablesquery->fetchArray(SQLITE3_ASSOC)) {
      $column_names[] = $column["name"];
    }
    foreach (array_keys($data) as $key) {
      if (!in_array($key, $column_names)) unset($data[$key]);
    }
    return $data;
  }
}
