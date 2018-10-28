<?php
# highly efficient regex that splits and trims a string by spaces and commas, but does not split by internal whitespaces i.e. the empty spaces between words
# https://stackoverflow.com/questions/19347005/how-can-i-explode-and-trim-whitespace#19347006
# preg_split('/\s*(\s*,\s*)*,+\s*(\s*,\s*)*/i', $input_string);
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");

function displayTable() {
  global $conn;
  $query = "SELECT * FROM cats";
  if (!$result = $conn->query($query)) die("failed to select from 'cats' table<br>");
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
}

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
$result = nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'Lion', 'leo', 4)");
$result = nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'Cougar', 'Growler', 4)");
$result = nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'Cheetah', 'Charly', 4)");

# Displaying data from 'cats' table
displayTable();

# Renaming the cheetah 'Charly' to 'Charlie'
/* $query = "UPDATE IGNORE cats SET name='Charlie' WHERE name='Charly'"; */
/* if (!$result = $conn->query($query)) die("Update of database failed"); */
/* displayTable(); */
?>
