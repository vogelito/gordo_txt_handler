<?php
  include 'mysql.php';

  // you might want to edit these vars
  $email = "some@gmail.com";
  $subject = "New Sandwich Order!";
  $food = "flour";


  $email_top = "";
  $response = "";

  // Parse Message from Twilio API
  $body = $_REQUEST["Body"];
  $pieces = explode (" ", $body);
  $size = count($pieces);
  $txt_food = strtolower($pieces[0]);
  $room = $body;//$pieces[1];
  $from = $_REQUEST["From"];

  function manage_error($resp) {
    echo $resp;
    echo "</Message></Response>";
    exit(0);
  }
  $sms_data = var_export($_REQUEST, true);
  $headers = 'From: "Pizza Master" <foodmaster@somedomain.com>' . "\r\n" .
    'Reply-To: some@gmail.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  header("content-type: text/xml");
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Message>
<?php
  if (atomic_read_contents($lock) == $DISABLED) {
    $response = "Dean Nohria ate the last roasted lamb sandwich, our bad :(  Please e-mail us at team@somedomain.com if you have any questions!";
  } else {
    if ($txt_food != $food) {
      $response = "Gordo no comprende! Text: FLOUR &lt;address&gt;. For example: FLOUR SFP 6 or FLOUR 85 Western Ave";
    } else {

      // MYSQL DB
      $con=mysqli_connect($host, $user, $password, $db);
      if (mysqli_connect_errno()) {
        $response = "We're sorry, we had some technical difficulties with this yummy sandwich! Please try again in 10 minutes!";
        $msg = "Failed to connect to MySQL: " . mysqli_connect_error();
        error_log($msg);
        manage_error($response);
      }

      $sql="INSERT INTO Orders (Phone, Room, Status, StartTime)
        VALUES
        ('$from','$room','PENDING',NOW())";

      if (!mysqli_query($con,$sql)) {
        $response = "So sorry! Our databases are all stuffed with sandwiches and we can't take any more orders. Please try again later!";
        error_log('Error: ' . mysqli_error($con));
        manage_error($response);
      }

      $id = mysqli_insert_id($con);
      mysqli_close($con);

      $email_top = "To accept order: ACCEPT\nTo reject order: REJECT\nPizza Majordomo!";
      $email_top = str_replace("ACCEPT", "http://somedomain.com/update.php?id=" . $id . "&status=$ACCEPTED", $email_top);
      $email_top = str_replace("REJECT", "http://somedomain.com/update.php?id=" . $id . "&status=$REJECTED", $email_top);

      $subject = $subject . " Address: " . $room;
      $response = "Order received for " . $room . "... You will receive confirmation if sandwiches are still available!";
    }
  }
  echo $response;
  // always e-mail the team
  $message = $email_top . "\n\n==========INCOMING MESSAGE================\n\n$sms_data\n\n==========SYSTEM RESPONSE=================\n\n$response";
  mail($email, $subject, $message, $headers);
?>

</Message>
</Response>
