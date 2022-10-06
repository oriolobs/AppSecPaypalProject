<?php

declare(strict_types=1);

namespace Pw\SlimApp\Model;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailVerification
{
    private $smtpHost;
    private $smtpPort;
    private $sender;
    private $password;
    public $receiver;
    private $code;
    private $code2;

    public function __construct($receiver)
    {
        $this->sender = "ebc00d8ea54ce9";
        $this->password = "e9c8aaee461f13";
        $this->receiver = $receiver;
        $this->smtpHost = "smtp.mailtrap.io";
        $this->smtpPort = 2525;
    }

    public function sendMail(string $verification_code){
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'tls';

        $mail->Host = $this->smtpHost;
        $mail->Port = $this->smtpPort;
        $mail->IsHTML(true);
        $mail->Username = $this->sender;
        $mail->Password = $this->password;
        $mail->Body=$this->getHTMLMessage($verification_code);
        $mail->Subject = "Your verification link is {$this->code}";
        $mail->SetFrom("pwpay.dont.replay@gmail.com","Verification link");
        $mail->AddAddress($this->receiver);

        if($mail->send()){
            //echo "MAIL SENT SUCCESSFULLY";
            echo "<script type='text/javascript'>
                alert('Please confirm your email before sign-in');
                window.location.href='/';
            </script>";

            // return true;
            exit;
          }
          echo "FAILED TO SEND MAIL";
          echo 'Mailer Error: ' . $mail->ErrorInfo;
          // return false;
    }

    public function getHTMLMessage(string $verification_code){
        $this->code= "http://slim-application.test:8030/activate?token=" . $verification_code;
        $this->code2= "http://pwpay.test/activate?token=" . $verification_code;
        $htmlMessage=<<<MSG
            <!DOCTYPE html>
            <html>
            <body>
                <h1>Select the link for verification of the account:</h1>
                <a href="url">{$this->code}</a>
                <p>Click this link to verify your account.</p>
                <p>For Homestead users with domain name "pwpay.test":</p>
                <a href="url">{$this->code2}</a>
            </body>
            </html>
        MSG;
        return $htmlMessage;
    }
}
