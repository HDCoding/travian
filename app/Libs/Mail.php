<?php

namespace Travian\Libs;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mail
{
    private $views = 'templates';

    public function __construct() { }

    private function __clone() { }

    public function confirmEmail($email, $key)
    {
        $mail = $this->getMailer();                                         // get instance of PHPMailer (including some additional info)
        $mail->addAddress($email);                                          // where you want to send confirmation email

        $link = URL . "/signup/activeacc/" . $key;                          // link for email confirmation
        $body = file_get_contents($this->views . "/emails/account-activation.php");         // load email HTML template

        $body = str_replace("{{website_name}}", 'Travian', $body);              // replace appropriate placeholders
        $body = str_replace("{{link}}", $link, $body);

        $mail->Subject = 'Travian' . " - Email confirmation.";                  // set subject and body
        $mail->Body = $body;

        // try to send the email
        if (!$mail->send()) {
            echo "Message can not be sent. <br />";
            echo "Mail error: " . $mail->ErrorInfo;
            exit();
        } else {
            echo "We have registered your invite request successfully! You will be contacted soon.";
        }

        $mail->clearAllRecipients();
    }

    public function resetPass($email, $key)
    {
        $mail = $this->getMailer();
        $mail->addAddress($email);

        $link = URL . "/recover/code/" . $key;
        $body = file_get_contents($this->views . "/emails/resetpass.php");

        $body = str_replace("{{ip}}", Helper::getIP(), $body);
        $body = str_replace("{{website_name}}", 'Travian', $body);
        $body = str_replace("{{link}}", $link, $body);

        $mail->Subject = 'Travian' . " - Password Reset.";
        $mail->Body = $body;

        if (!$mail->send()) {
            echo "Message can not be sent. <br />";
            echo "Mail error: " . $mail->ErrorInfo;
            exit();
        } else {
            echo "We have registered your invite request successfully! You will be contacted soon.";
        }

        $mail->clearAllRecipients();
    }

    public function invite($email, $key)
    {
        $mail = $this->getMailer();
        $mail->addAddress($email);

        $link = URL . "/signup/invite/" . $key;
        $body = file_get_contents($this->views . "emails/invite.php");

        $body = str_replace("{{website_name}}", 'Travian', $body);
        $body = str_replace("{{invlink}}", $link, $body);

        $mail->Subject = 'Travian' . " - user invitation confirmation.";
        $mail->Body = $body;

        if (!$mail->send()) {
            echo "Message can not be sent. <br />";
            echo "Mail error: " . $mail->ErrorInfo;
            exit();
        } else {
            echo "We have registered your invite request successfully! You will be contacted soon.";
        }

        $mail->clearAllRecipients();
    }

    public function thanks($email)
    {
        $mail = $this->getMailer();

        $mail->addAddress($email);

        $body = file_get_contents($this->views . "emails/thanks.php");

        $body = str_replace("{{website_name}}", 'Travian', $body);
        $body = str_replace("{{rulink}}", URL . '/rules', $body);
        $body = str_replace("{{falink}}", URL . '/faq', $body);

        $mail->Subject = 'Travian' . " - Thank you for sign-up.";
        $mail->Body = $body;

        if (!$mail->send()) {
            echo "Message can not be sent. <br />";
            echo "Mail error: " . $mail->ErrorInfo;
            exit();
        } else {
            echo "We have registered your account.";
        }

        $mail->clearAllRecipients();
    }

    /**
     * Private area
     */
    private function getMailer()
    {
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = SMTP_HOST;                              // Specify main and backup SMTP servers
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = SMTP_USERNAME;                      // SMTP username
            $mail->Password = SMTP_PASSWORD;                      // SMTP password
            $mail->SMTPSecure = SMTP_ENCRYPTION;                  // Enable TLS encryption, `ssl` also accepted
            $mail->Port = SMTP_PORT;                              // TCP port to connect to

            $mail->CharSet = "UTF-8";
            $mail->isHTML(true);                                    // tell mailer that we are sending HTML email

            $mail->From = FROM_MAIL;
            $mail->FromName = 'Travian';
            $mail->addReplyTo(FROM_MAIL, 'Travian');
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        return $mail;
    }
}