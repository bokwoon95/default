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

?>
