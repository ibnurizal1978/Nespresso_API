<?php
ini_set('display_errors',1);  error_reporting(E_ALL);
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
require 'plugins/PHPMailer/src/Exception.php';
require 'plugins/PHPMailer/src/PHPMailer.php';
require 'plugins/PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->SMTPSecure = 'tls';
$mail->Host = "smtp-relay.sendinblue.com";
//$mail->SMTPDebug = 4;
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Timeout = 60;
$mail->SMTPKeepAlive = true;

$mail->Username   = '***';
$mail->Password   = '***';
$mail->setFrom('***', 'Trinax');
//$mail->addAddress('***', 'Godeg');
$mail->addAddress('***', 'Si Ying Kay');
$mail->addAddress('***', 'Kwee Fong');

//attachment
$path=dirname(__FILE__,2)."/nespresso/reports/nespresso-weekly-report-".date('dmY').".xls";

if(file_exists($path))
{
  $name="nespresso-weekly-report-".date('dmY').".xls";
  $mail->AddAttachment($path,$name,$encoding ='base64',$type = 'application/octet-stream');
}

$mail->isHTML(true);
$mail->Subject = 'Nespresso Weekly Report';
$mail->Body    = 'Nespresso Weekly Report';

if(!$mail->send()) {
    echo 'Message was not sent.';
    echo 'Mailer error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
?>
