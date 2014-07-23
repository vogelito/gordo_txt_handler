<?php
  include 'mysql.php';

  // Email vars
  $email = "field3@somedomain.com";
  $subject = "Section A Sent a Message!";
  $response = "";

  // Parse Message from Twilio API
  $body = strtolower($_REQUEST["Body"]);
  $pieces = explode (" ", $body);
  $size = count($pieces);
  $from = $_REQUEST["From"];

  function manage_error($resp) {
    echo $resp;
    echo "</Message></Response>";
    exit(0);
  }
  $sms_data = var_export($_REQUEST, true);
  $headers = 'From: "Gordo" <foodmaster@somedomain.com>' . "\r\n" .
    'Reply-To: field3@somedomain.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

  header("content-type: text/xml");
  echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response>
    <Message>
<?php
  if (atomic_read_contents($lock) == $DISABLED) {
    $response = "Gordo is currently taking it easy in the beach... Please e-mail us at field3@somedomain.com if you have any questions!";
  } else {
    switch($body) {
      case "yes":
        $response = "Gordo just sharted himself a little bit out of sheer happiness! If you want this to be real, buy FATY!!! (Fred approves this message)";
        break;
      case "no":
        $response = "Well, crazy sexy kale salad is a shitty product anyway. It was Marissa's idea. Reply YES OTHERS for other options or NO I HATE YOU to slap Gordo around";
        break;
      case "yes others":
        $response = "With your support, Gordo could deliver many many things (pizza, thai, chinese, salads, Alex Sloane a la Vodka). Reply YES GORDO to show support or NO I HATE YOU";
        break;
      case "yes gordo":
        $response = "Gordo loves you! Vote for him, he'll make you proud. (that's $2,500 more drunk food your way at 2AM). Could be useful after Max's next Safari Dinner! Buy FATY!";
        break;
      case "no i hate you":
        $response = "Well, we tried. We hope you never ever desire quick food in your life. Because you'll be in trouble. Depressed Gordo will now go eat his inventory. Buy FATY?";
        break;
      case "gordo":
        $response = "Hi!!!\nMy name is Gordo! I'm a little chubby man who loves food and gets excited about stupid things. Like Santi's moustache. Buy FATY!";
        break;
      default:
        $response = "For an HBS student, you suck at following directions. Text GORDO for help. Or ask Saul. I think he gets it. Now go buy FATY";
    }




  }
  echo $response;
  // always e-mail the team
  $message = "==========INCOMING MESSAGE================\n\n$sms_data\n\n==========SYSTEM RESPONSE=================\n\n$response";
  mail($email, $subject, $message, $headers);
?>

</Message>
</Response>
