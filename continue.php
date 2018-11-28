<?php
require_once 'helpers.php';

session_start();
if (isset($_SESSION['forename'])) {
  $forename = htmlspecialchars($_SESSION['forename']);
  $surname = htmlspecialchars($_SESSION['surname']);
  destroy_session_and_data();

  echo "Welcome back $forename.<br>
    Your full name is $forename $surname.<br>";
} else {
  echo "Please <a href='authenticate2.php'>click here</a> to log in.";
}
?>
