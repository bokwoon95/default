<?php
require_once 'login.php';
require_once 'helpers.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");

$a = "a";
$c = "c";
test(isset($b),"isset_b");
test(isset($a),"isset_a");

?>
