<?php

  require 'twilio-php-master/Services/Twilio.php';

  // Twilio
  $sid = "TWILIO_SID";
  $token = "TWILIO_TOKEN";
  $twilio_phone = "TWILIO_PHONE";

  // MYSQL Vars
  $host = "host";
  $user = "user";
  $password = "pw";
  $db = "db_name";

  // Files
  $lock = "state.lock";

  // states
  $DISABLED = "DISABLED";
  $ENABLED = "ENABLED";
  $PENDING = "PENDING";
  $ACCEPTED = "ACCEPTED";
  $REJECTED = "REJECTED";
  $CANCELLED = "CANCELLED";
  $DELIVERED = "DELIVERED";
  $CUSTOM = "CUSTOM_MSG";
  $OUTSIDE_DELIVERY = "OUTSIDE_DELIVERY";

  function atomic_put_contents($filename, $data) {
    // Copied largely from http://php.net/manual/en/function.flock.php
    $fp = fopen($filename, "w+");
    if (flock($fp, LOCK_EX)) {
      fwrite($fp, $data);
      flock($fp, LOCK_UN);
    }
    fclose($fp);
  }

  function atomic_read_contents($filename) {
    // Copied largely from http://php.net/manual/en/function.flock.php
    $fp = fopen($filename, "r+");
    if (flock($fp, LOCK_EX)) { 
      $contents = fread($fp, filesize($filename));
      flock($fp, LOCK_UN);
    }
    fclose($fp);
    return $contents;
  }
?>
