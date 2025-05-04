<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;
//send email function using PHPMailer

if(!function_exists('sendEmail')){
    function sendEmail($mailConfig){
        require 'PHPMailer/src/Exception.php';
        require 'PHPMailer/src/PHPMailer.php';
        require 'PHPMailer/src/SMTP.php';

        $mail = new PHPMailer(true);

         //Server settings
        $mail->SMTPDebug = 0;
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = env('EMAIL_HOST');                    //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = env('EMAIL_USERNAME');                  //SMTP username
        $mail->Password   = env('EMAIL_PASSWORD');                               //SMTP password
        $mail->SMTPSecure = env('EMAIL_ENCRYPTION');       //Enable implicit TLS encryption
        $mail->Port       = env('EMAIL_PORT');                                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($mailConfig['mail_from_email'],$mailConfig['mail_from_name']);
        $mail->addAddress($mailConfig['mail_recipient_email'],$mailConfig['mail_recipient_name']);     //Add a recipient


        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $mailConfig['mail_subject'];
        $mail->Body    = $mailConfig['mail_body'];

        if($mail->send()){
            return true;
        }else{
            return false;
        }

    }
}
