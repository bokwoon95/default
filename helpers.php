<?php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");

function test($bool,$name="variable") {
  if ($bool) {
    echo "$name value is TRUE<br>";
  } else {
    echo "$name value is FALSE<br>";
  }
}

function s($i=1) {
  # helper function that inserts <br> for specified number of times
  while ($i-->0) {
    echo "<br>";
  }
}

function transform($arr) {
  # ["KEY1='VALUE1'", "KEY2='VALUE2'", "KEY3='VALUE3'", "KEY4='VALUE4'"]
  # converted into
  # ['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2', 'KEY3' => 'VALUE3', 'KEY4' => 'VALUE4']
  $result = [];
  $unpacked = [];
  foreach ($arr as $string) {
    preg_match('/(?<key>\w+)=\'(?<value>\w+)/i', $string, $unpacked);
    $key   = $unpacked['key'];
    $value = $unpacked['value'];
    $result[$key] = $value;
  }
  return $result;
}

function untransform($arr) {
  # ['KEY1' => 'VALUE1', 'KEY2' => 'VALUE2', 'KEY3' => 'VALUE3', 'KEY4' => 'VALUE4']
  # converted into
  # ["KEY1='VALUE1'", "KEY2='VALUE2'", "KEY3='VALUE3'", "KEY4='VALUE4'"]
  $result = [];
  foreach ($arr as $key => $value) {
    $result[] = sprintf("%s=%s", $key, $value);
  }
  return $result;
}

function getColumnInfo($table_name, $database="publications") {
  # returnvalue['col_count'] : number of columns
  # returnvalue['col_names'] : array of all column names
  global $conn;

  # get number of columns as col_count
  $column_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table_name'";
  if (!$column_table = $conn->query($column_query)) die("'$column_query' failed :DDD");
  $col_count = $column_table->num_rows;

  # populate col_names[] with all column names
  for ($j=0; $j<$col_count; $j++) {
    $row = $column_table->fetch_array(MYSQLI_ASSOC);
    $col_names[] = $row['COLUMN_NAME'];
  }

  $returnvalue['col_count'] = $col_count;
  $returnvalue['col_names'] = $col_names;
  return $returnvalue;
}

function displayTable($result, $table_name, $database="publications") {
  global $conn;
  $cols = getColumnInfo("classics")['col_count'];
  $rows = $result->num_rows;
  echo "<table>";
  for ($j=0; $j<$rows; $j++) {
    $row = $result->fetch_array(MYSQLI_NUM);
    echo "<tr>";
    for ($k=0; $k<$cols; $k++) {
      echo "<td> | " . htmlspecialchars($row[$k]) . "</td>";
    }
    echo "</tr>";
  }
  echo "</table>";
}

function nonDuplicateInsert($insertcmd) {
  global $conn;
  preg_match('/insert into (\w+) values\((.+)\)/i', $insertcmd, $arr);
  if (count($arr) != 3) { $errmsg="regex match failure for \$insertcmd"; return $errmsg; }
  $table = $arr[1];
  $args_str = $arr[2];

  $values_array = preg_split('/\s*(\s*,\s*)*,+\s*(\s*,\s*)*/i', $args_str);
  $table_column_info = getColumnInfo($table);
  $col_count = $table_column_info['col_count'];
  $col_names = $table_column_info['col_names'];
  if (count($values_array) != $col_count) { $errmsg="wrong number of values in \$insertcmd"; return $errmsg; }

  for ($i=0; $i<$col_count; $i++) {
    if ($values_array[$i] != 'NULL') {
      $values_alist[$col_names[$i]] = trim($values_array[$i]);
    }
  }

  $values_alist_stringified = implode(" AND ", untransform($values_alist));
  $query_sel = "SELECT * FROM $table WHERE $values_alist_stringified";
  if (!$result_sel = $conn->query($query_sel)) die("'$query_sel' failed :DDD");

  if ($result_sel->num_rows == 0) {
    if (!$result_ins = $conn->query($insertcmd)) die("'$insert_cmd' failed :DDD");
    return "entry inserted\n";
  } else {
    return "entry already exists\n";
  }
  return "nonDuplicateInsert_end\n";
}

function nonDuplicateUpdate($updatecmd) {
  global $conn;
  preg_match('/UPDATE (?<table>\w+) SET (?<set>.+) WHERE (?<where>.+)/i', $updatecmd, $arr);
  $table = $arr['table'];

  $set_str = $arr['set'];
  $set_arr = preg_split('/\s*(\s*,\s*)*,+\s*(\s*,\s*)*/i', $set_str);
  $set_arr_transformed = transform($set_arr);

  $where_str = $arr['where'];
  $where_arr = preg_split('/\s*(\s*,\s*)*and+\s*(\s*,\s*)*/i', $where_str);
  $where_arr_transformed = transform($where_arr);

  $result = $conn->query("SELECT * from $table WHERE $where_str");
  $rows = $result->num_rows;
  for ($j=0; $j<$rows; $j++) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $ttable[] = $row;
  }

  $ttable_len = sizeof($ttable);
  ##If table_len = 0, there's nothing to update. Return immediately.
  for ($j=0; $j<$ttable_len; $j++) {
    $ttable_row = $ttable[$j];
    $ttable_row_to_search = [];
    foreach ($ttable_row as $key => $value) {
      $ttable_row_to_search[] = $set_arr_transformed[$key] ? $set_arr_transformed[$key] : $ttable_row[$key];
    }
  }
}

?>
