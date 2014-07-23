<?php
  include 'mysql.php';

  $to_state = $_GET["state"];
  atomic_put_contents($lock, $to_state);

  header("Location: stats.php");
?>
