<?php
if ($_SERVER['HTTP_HOST'] == "localhost") {
  $host = "localhost";
  $user = "root";
  $password = "";
  $db = "job_portal";
} else {
  $host = "localhost";
  $user = "job_user";
  $password = "j)r[d-2~3!Qn";
  $db = "job_portal";
}

try {
  $conn = new mysqli($host, $user, $password, $db);
} catch (Exception $e) {
  echo "<script>console.log('" . ($e->getMessage()) . "')</script>";
}
