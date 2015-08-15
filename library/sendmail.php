<?php
  /**
   * Sets error header and json error message response.
   *
   * @param  String $messsage error message of response
   * @return void
   */
   openlog("sendmailLog", LOG_PID | LOG_PERROR, LOG_LOCAL0);
   
  function errorResponse ($messsage) {
    header('HTTP/1.1 500 Internal Server Error');
    die(json_encode(array('message' => $messsage)));
  }

  /**
   * Pulls posted values for all fields in $fields_req array.
   * If a required field does not have a value, an error response is given.
   */
  function constructMessageBody () {
    $fields_req =  array("name" => true, "email" => true, "message" => true);
    $message_body = "";
    foreach ($fields_req as $name => $required) {
      $postedValue = $_POST[$name];
      if ($required && empty($postedValue)) {
        errorResponse("$name is empty.");
      } else {
        $message_body .= ucfirst($name) . ":  " . $postedValue . "\n";
      }
    }
    return $message_body;
  }

  header('Content-type: application/json');
  
  
    $access = date("Y/m/d H:i:s");
    syslog(LOG_INFO, "Sending Captcha: $access value: ({$_SERVER['RECAPTCHA_SECRET_KEY']})");
  

  //do Captcha check, make sure the submitter is not a robot:)...
  $url = 'https://www.google.com/recaptcha/api/siteverify';
 $opts = array('http' =>
    array(
      'method'  => 'POST',
      'header'  => 'Content-type: application/x-www-form-urlencoded',
      'content' => http_build_query(array('secret' => $_SERVER['RECAPTCHA_SECRET_KEY'], 'response' => $_POST["g-recaptcha-response"]))
    )
  );
  
    $access = date("Y/m/d H:i:s");
    syslog(LOG_INFO, "Sending Captcha: $access {$opts} ({$_SERVER['HTTP_USER_AGENT']})");
  $context  = stream_context_create($opts);
  $result = json_decode(file_get_contents($url, false, $context, -1, 40000));

  if (!$result->success) {

    $access = date("Y/m/d H:i:s");
  syslog(LOG_WARNING, "Captcha Failure: $access {$opts} ({$_SERVER['HTTP_USER_AGENT']})");
    errorResponse('reCAPTCHA checked failed!');
  }
 ////attempt to send email
$messageBody = constructMessageBody();
//require './vender/php_mailer/PHPMailerAutoload.php';
//$mail = new PHPMailer;
//$mail->CharSet = 'UTF-8';
//$mail->isSMTP();
//$mail->Host = _SERVER['FEEDBACK_HOSTNAME'];
  
i//f (_SERVER['FEEDBACK_ENCRYPTION'] == 'TLS') {
//    $mail->SMTPSecure = 'tls';
//    $mail->Port = 587;
//} elseif (_SERVER['FEEDBACK_ENCRYPTION'] == 'SSL') {
//    $mail->SMTPSecure = 'ssl';
//    $mail->Port = 465;
//}

//$mail->setFrom($_POST['email'], $_POST['name']);
//$mail->addAddress(_SERVER['FEEDBACK_EMAIL'));

//$mail->Subject = $_POST['reason'];
//$mail->Body  = $messageBody;


////try to send the message
//if($mail->send()) {
//    echo json_encode(array('message' => 'Your message was successfully submitted.'));
//    } else {
//    errorResponse('An expected error occured while attempting to send the email: ' . $mail->ErrorInfo);
//    }

//$headers = 'From:'.  $_POST['email']. "\r\n" .    'Reply-To:' .  _SERVER['FEEDBACK_EMAIL']  . "\r\n" . 'X-Mailer: PHP/' . phpversion();

$mailed = mail(_SERVER['FEEDBACK_EMAIL'] , "Someone wants you to contact them.", $messageBody);
if($mailed){
    echo "Email was sent!";
}else{
    echo "Email was not sent :(";
}

?>
