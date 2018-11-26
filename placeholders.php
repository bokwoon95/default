<?php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");

function nonDuplicateInsert($insertcmd) {
  global $conn;
  preg_match('/insert into (\w+) values\((.+)\)/i', $insertcmd, $arr);
  if (count($arr) != 3) { $errmsg="regex match failure for \$insertcmd"; return $errmsg; }
  $table = $arr[1];
  $args_str = $arr[2];

  $values_array = preg_split('/\s*(\s*,\s*)*,+\s*(\s*,\s*)*/i', $args_str);
  if (count($values_array) != 4) { $errmsg="wrong number of values in \$insertcmd"; return $errmsg; }

  $id     = trim($values_array[0]);
  $family = trim($values_array[1]);
  $name   = trim($values_array[2]);
  $age    = trim($values_array[3]);

  $query_sel = "SELECT * FROM $table where family=$family and name=$name and age=$age";
  if (!$result_sel = $conn->query($query_sel)) die("'$query_sel' failed :DDD");

  if ($result_sel->num_rows == 0) {
    if (!$result_ins = $conn->query($insertcmd)) die("'$insert_cmd' failed :DDD");
    return "entry inserted";
  } else {
    return "entry already exists";
  }
  return "nonDuplicateInsert_end";
}

function displayTable($result, $table_name, $database="publications") {
  global $conn;
  $column_query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='$database' AND TABLE_NAME='$table_name'";
  if (!$column_table = $conn->query($column_query)) die("'$column_query' failed :DDD");
  $cols = $column_table->num_rows;
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

# testing displayTable()
if (!$result = $conn->query("SELECT * FROM classics")) die("yeet");
displayTable($result, "classics");

?>
