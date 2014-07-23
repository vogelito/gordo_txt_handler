<?php

  include 'mysql.php';

  $food = "Sandwich";
  $price = "$15";
  $time = "15 minutes";

  $status = $_GET["status"];
  $id = $_GET["id"];
  $custom_msg = $_GET["message"];

  $con=mysqli_connect($host, $user, $password, $db);
  if (mysqli_connect_errno()) {
    $msg = "Failed to connect to MySQL: " . mysqli_connect_error();
    echo $msg;
    error_log($msg);
  }

  $endTime = "";
  if ($status == "CANCELLED" || $status == "REJECTED" || $status == "DELIVERED" || $status == $OUTSIDE_DELIVERY) {
    $endTime = ", EndTime=NOW() ";
  }

  // Only update status and endtime if it's not a custom text message
  if ($status != $CUSTOM_MSG) {
    $sql = "UPDATE Orders SET Status='$status' $endTime WHERE ID='$id'";
    if (!mysqli_query($con,$sql)) {
      die('Error: ' . mysqli_error($con));
    }
  }

  $send_msg = true;
  $msg = "";
  switch($status) {
    case $ACCEPTED:
      $msg = "Your $food is on its way and will be delivered within $time! Get your $price cash ready!";
      break;
    case $REJECTED:
      $msg = "Sorry, we ran out of $food unfortunately! We'll text you when we start taking orders again";
      break;
    case $OUTSIDE_DELIVERY:
      $msg = "Sorry, your address is outside of the delivery area, so Gordo won't be able to deliver your $food unfortunately!";
      break;
    case $CANCELLED:
      $msg = "Our most sincere apologies! Gordo ate the last $food and we have to cancel your order :(";
      break;
    case $DELIVERED:
      $msg = "We hope you enjoyed your $food! Send us any feedback/suggestions to team@somedomain.com";
      break;
    case $CUSTOM_MSG:
      $msg = $custom_msg;
      break;
    default:
      $send_msg = false;
  }
  if ($send_msg) {
    $result = mysqli_query($con,"SELECT * FROM Orders WHERE ID='$id'");

    while($row = mysqli_fetch_array($result)) {
      $to_phone = $row['Phone'];
      $room = $row['Room'];
      $client = new Services_Twilio($sid, $token); 
      $client->account->messages->sendMessage($twilio_phone, $to_phone, $msg);
    }
  }

  mysqli_close($con);

  header("Location: stats.php");
?>
