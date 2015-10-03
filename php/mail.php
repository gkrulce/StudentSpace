<?php
function sendMail($to, $subject, $body)
{
  require '../lib/PHPMailer/PHPMailerAutoload.php';

$mail = new PHPMailer;

$mail->isSMTP();                                      // Set mailer to use SMTP
$mail->Host = 'smtp.ucsd.edu;studygroups.ucsd.edu';                     // Specify main and backup SMTP servers
$mail->SMTPSecure = 'tls';                            // Enable encryption, only 'tls' is accepted

$mail->AddReplyTo('support@studygroups.ucsd.edu', 'StudentSpace');
$mail->From = 'no-reply@studygroups.ucsd.edu';
$mail->FromName = 'StudentSpace';
$mail->addAddress($to);                 // Add a recipient

$mail->WordWrap = 50;                                 // Set word wrap to 50 characters

$mail->Subject = $subject;
$mail->Body    = $body;

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
}
//sendMail("gkrulce@ucsd.edu", "It worked!", "Body text");
?>
