<?php
function test($bool,$name="variable") {
  if ($bool) {
    echo "$name value is TRUE<br>";
  } else {
    echo "$name value is FALSE<br>";
  }
}

$a = "a";
$c = "c";
test(isset($b),"isset_b");
test(isset($a),"isset_a");
?>
