<?php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");

# create 'cats' table if it doesn't exist
if ($result = $conn->query("SHOW TABLES LIKE 'cats'")) {
  if ($result->num_rows == 1) {
    echo "'cats' table already exists<br>";
  } else {
    $query = "CREATE TABLE cats (
      id SMALLINT NOT NULL AUTO_INCREMENT
      ,family VARCHAR(32) NOT NULL
      ,name VARCHAR(32) NOT NULL
      ,age TINYINT NOT NULL
      ,PRIMARY KEY (id)
    )";
    $result = $conn->query($query);
    if (!$result) die("Failed to create 'cats' table<br>");
  }
}

# Describe the 'cats' table
$query = "DESCRIBE cats ";
if (!$result = $conn->query($query)) die("failed to describe 'cats' table<br>");
$rows = $result->num_rows;
echo "<br><table><tr><th>Column</th><th>Type</th><th>NULL</th><th>Key</th></tr>";
for ($j=0; $j<$rows; $j++) {
  $row = $result->fetch_array(MYSQLI_NUM);
  echo "<tr>";
  for ($k=0; $k<4; ++$k) {
    echo "<td>" . htmlspecialchars($row[$k]) . "</td>";
  }
}
echo "</tr>";
echo "</table>";

# Drop the 'cats' table
/* $query = "DROP TABLE cats"; */
/* if (!$result = $conn->query($query)) die("Failed to drop 'cats' table<br>"); */

# Insert data into the 'cats' table
function nonDuplicateInsert($insertcmd) {
  global $conn;
  preg_match('/insert into \w+ values\((.+)\)/i', $insertcmd, $arr);
  if (count($arr) != 2) { echo "failed to parse \$insertcmd"; return false; }
  $args_str = $arr[1];
  $value_array = preg_split('/,/i', $args_str);
  if (count($value_array) != 4) { echo "wrong number of values in \$insertcmd"; return false; }
  $id = trim($value_array[0]);
  $family = trim($value_array[1]);
  $name = trim($value_array[2]);
  $age = trim($value_array[3]);
  $query_sel = "SELECT * FROM cats where family=$family and name=$name and age=$age";
  if (!$result_sel = $conn->query($query_sel)) die("failed :DDD");
  if ($result_sel->num_rows == 0) {
    $result = $conn->query($insertcmd);
  }
  return $result;
}
nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'LION', 'LEO', 4)");

# Displaying data from 'cats' table
$query = "SELECT * FROM cats";
if (!$result = $conn->query($query)) die("failed to insert into 'cats' table<br>");
$rows = $result->num_rows;
echo "<br><table><tr><th>Id</th><th>Family</th><th>Name</th><th>Age</th></tr>";
for ($j=0; $j<$rows; $j++) {
  $row = $result->fetch_array(MYSQLI_NUM);
  echo "<tr>";
  for ($k=0; $k<4; $k++) {
    echo "<td>" . htmlspecialchars($row[$k]) . "</td>";
  }
  echo "</tr>";
}
echo "</table>";
?>
