<?php
# highly efficient regex that splits and trims a string by spaces and commas, but does not split by internal whitespaces i.e. the empty spaces between words
# https://stackoverflow.com/questions/19347005/how-can-i-explode-and-trim-whitespace#19347006
# preg_split('/\s*(\s*,\s*)*,+\s*(\s*,\s*)*/i', $input_string);
require_once 'login.php';
require_once 'helpers.php';
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
$result = nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'Lion', 'leo', 4)");
$result = nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'Cougar', 'Growler', 4)");
$result = nonDuplicateInsert("INSERT INTO cats VALUES(NULL, 'Cheetah', 'Charly', 4)");

# Displaying data from 'cats' table
if (!$result=$conn->query("SELECT * FROM cats")) die("nohomo");
s();
displayTable($result, "cats");

# Renaming the cheetah 'Charly' to 'Charlie'
# Warning! This has the effect of creating duplicate "Charlie" entries
#   because I haven't figured out how to do a non-duplicate update
/* $query = "UPDATE IGNORE cats SET name='Charlie' WHERE name='Charly'"; */
/* if (!$result = $conn->query($query)) die("Update of database failed"); */
/* displayTable(); */
?>
