<?php
function test($bool,$name="variable") {
  if ($bool) {
    echo "$name value is TRUE<br>";
  } else {
    echo "$name value is FALSE<br>";
  }
}

function s($i=1) {
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
    $result[] = $sprintf("%s='%s'", $key, $value);
  }
  return $result;
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
    # Untransform $ttable_row_to_search
    # $where_requirements = implode $ttable_row_to_search
    # $query = "SELECT * from $table where $where_requirements"
    # $result = execute $query
    # if ($result->num_rows > 0) {
    #   execute 
  }
}
?>
