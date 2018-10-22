<?php // selectclassicsv1.php
require_once 'testconnection.php';
$query = "SELECT * FROM classics";
$result = $conn->query($query);
$type_of_result_var = gettype($result);
if (!$result) die("Fatal Error");
else echo <<<_END
  <p>The query "SELECT * FROM classics" executed successfully & is stored in \$result ($type_of_result_var)</p>
_END;
$rows = $result->num_rows;
for ($j=0; $j<$rows; $j++) {
  $result->data_seek($j);
  echo 'Author: '.htmlspecialchars($result->fetch_assoc()['author']) .'<br>';
  $result->data_seek($j);
  echo 'Title: '.htmlspecialchars($result->fetch_assoc()['title']) .'<br>';
  $result->data_seek($j);
  echo 'Category: '.htmlspecialchars($result->fetch_assoc()['category']) .'<br>';
  $result->data_seek($j);
  echo 'Year: '.htmlspecialchars($result->fetch_assoc()['year']) .'<br>';
  $result->data_seek($j);
  echo 'Isbn: '.htmlspecialchars($result->fetch_assoc()['isbn']) .'<br><br>';
}

$result->close();
$connection->close();
?>
