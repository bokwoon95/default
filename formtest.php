<?php
if (isset($_POST['name']))
  $name = htmlentities($_POST['name']);
else
  $name = "(not entered)";
echo <<<_END
<html>
  <head>
    <title>Form Test</title>
  </head>
  <body>
  Your name is: $name<br>
  <form method="post" action="formtest.php" autocomplete="off">
    What is your name?
    <input type="text" name="name" autocomplete="off">
    <input type="submit">
    <input type="hidden" name="submitted" value="yes">
  </form>
  </body>
</html>
_END;
?>
