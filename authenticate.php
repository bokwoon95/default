<?php
# needed for trigger_error to report the error type
# error_reporting(E_ALL);
# ini_set('display_errors', '1');

require_once 'login.php';
require_once 'helpers.php';

$conn = new mysqli($hn, $un, $pw, $db);

if ($conn->connect_error) die("Fatal Error");
# $conn->connect_error or trigger_error($conn->error, E_USER_ERROR);

while (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
  header('WWW-Authenticate: Basic realm="Restricted Area"');
  header('HTTP/1.0 401 Unauthorized');
  print_r($_SERVER);
}

if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW'])) {

  $un_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_USER']);
  $pw_temp = mysql_entities_fix_string($conn, $_SERVER['PHP_AUTH_PW']);

  $query = "SELECT * FROM users where username='$un_temp'";
  $result = $conn->query($query) or trigger_error($conn->error, E_USER_ERROR);

  if ($result->num_rows) {
    $row = $result->fetch_array(MYSQLI_ASSOC);
    $result->close();
    if (password_verify($pw_temp, $row['password'])) {
      session_start();
      $forename = $row['forename'];
      $surname = $row['surname'];
      $username = $row['username'];
      $_SESSION['forename'] = $forename;
      $_SESSION['surname'] = $surname;
      echo htmlspecialchars("$forename $surname : Hi $forename, you are now logged in as '$username'");
      die("<p><a href='continue.php'>Click here to continue</a></p>");
    }
    else trigger_error("Invalid username/password combination", E_USER_ERROR);
  } else {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.0 401 Unauthorized');
    trigger_error("Please enter your username and password");
  }
}

$conn->close();
?>
