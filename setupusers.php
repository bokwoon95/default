<?php
# needed for trigger_error to report the error type
# error_reporting(E_ALL);
# ini_set('display_errors', '1');

require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) die("Fatal error");
# $result = $conn->connect_error or trigger_error($conn->error, E_USER_ERROR);

$query = "CREATE TABLE users (
  forename VARCHAR(32) NOT NULL
  ,surname VARCHAR(32) NOT NULL
  ,username VARCHAR(32) NOT NULL UNIQUE
  ,password VARCHAR(255) NOT NULL
)";

$conn->query($query) or trigger_error($conn->error, E_USER_ERROR);
# note that passing in 'E_USER_ERROR' an optional argument will interrupt script execution

$forename = "Bill";
$surname  = "Smith";
$username = "bsmith";
$password = "mysecret";
$hash     = password_hash($password, PASSWORD_DEFAULT);

add_user($conn, $forename, $surname, $username, $hash);

$forename = "Pauline";
$surname  = "Jones";
$username = "pjones";
$password = "acrobat";
$hash     = password_hash($password, PASSWORD_DEFAULT);

add_user($conn, $forename, $surname, $username, $hash);

function add_user($conn, $fn, $sn, $un, $pw) {
  global $conn;
  $stmt = $conn->prepare('INSERT INTO users VALUES(?,?,?,?)');
  $stmt->bind_param('ssss', $fn, $sn, $un, $pw);
  $stmt->execute();
  $stmt->close();
}
?>
