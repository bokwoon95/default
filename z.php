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
  while ($i-->0) {
    echo "<br>";
  }
}

$a = "a";
$c = "c";
test(isset($b),"isset_b");
test(isset($a),"isset_a");


$yeet = "INSERT INTO cats VALUES(NULL, 'LION', 'LEO', 4)";
function nonDuplicateInsert($insertcmd) {
  global $conn;
  preg_match('/insert into \w+ values\((.+)\)/i', $insertcmd, $arr);s();
  print_r($arr);s();
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
nonDuplicateInsert($yeet);

?>
