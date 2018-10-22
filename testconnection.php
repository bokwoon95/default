<?php // testconnection.php
require_once 'login.php';
$conn = new mysqli($hn, $un, $pw, $db);
if ($conn->connect_error) die("Fatal Error");
else echo <<<_END
  <p>Access to the database <i>$db</i> works<br>
  Username: $un<br>
  Password: $pw</p>
_END;
?>
