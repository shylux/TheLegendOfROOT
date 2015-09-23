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

    $sql = "SELECT COUNT (" . $desired[0] . ") FROM '{$table}'";
    $sql .= ( $where === false ) ? '' : " WHERE " . implode(' AND ', $this->buildUpdateCondition($where)) ;

    return parent::querySingle($sql);
  }

  public function select( $table, $desired, $where = false )
  {
    if ( !is_string($table) || !is_array($desired) || ( !$this->hasTable($table) && $table !== MASTERTABLE ) ){
      return false;
    }

    $sql = "SELECT " . implode(",", $desired) . " FROM '{$table}'";
    $sql .= ( $where === false ) ? '' : " WHERE " . implode(' AND ', $this->buildUpdateCondition($where)) ;

    $result = parent::query($sql);

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
    $fields = array_keys($data);

      if ( !is_string($table) || !is_array($data) || !$this->hasTable($table) ){
      return false;
    }

    $sql = "INSERT INTO '{$table}' ('" . implode("','", $fields) . "') VALUES (" . implode(",", $this->buildInsertCondition($data)) . ")";

    return parent::query($sql);
  }

  public function remove( $table, $where )
  {
    if ( !is_string($table) || !is_array($where) || count($where) === 0 )
    {
      return false;
    }

    $sql = "DELETE FROM '{$table}' WHERE " . implode(' AND ', $this->buildUpdateCondition($where));

    return ( parent::query($sql) ) ? true : false ;
  }

  public function update( $table, $data, $where )
  {
    if ( !is_string($table) || !is_array($data) || !is_array($where) )
    {
      return false;
    }

    $sql = "UPDATE '{$table}' SET " . implode(', ', $this->buildUpdateCondition($data)) . " WHERE " . implode(' AND ', $this->buildUpdateCondition($where));

    return parent::query($sql);
  }

  private function buildInsertCondition( $data )
  {
    $preparedData = $this->helperCondition($data);

    foreach ( $preparedData as $value )
    {
      $condition[] = $value;
    }

    return $condition;
  }

  private function buildUpdateCondition( $data )
  {
    $preparedData = $this->helperCondition($data);

    foreach ( $preparedData as $key => $value )
    {
      $condition[] = "`{$key}` = {$value}";
    }

    return $condition;
  }

  private function helperCondition( $data )
  {
    foreach ( $data as $key => $value )
    {
      switch ( gettype($value) )
      {
        case 'boolean':
        case 'NULL':
          $value = 'NULL';
          break;

        case 'string':
          $value = "'" . parent::escapeString($value) . "'";
          break;

        case 'double':
        case 'integer':
          $value = $value;
          break;

        default:
          $value = "'" . parent::escapeString($value) . "'";
      }
      $preparedData[$key] = $value;
    }
    return $preparedData;
  }
}

$db = new SQLITE("/Users/lukas/projects/TheLegendOfROOT/root.db");
