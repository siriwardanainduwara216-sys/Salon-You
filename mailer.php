<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/PHPMailer.php';

function send_otp_email($to_email, $user_name, $otp_code) {
    $mail = new PHPMailer(true);

    try {
        // Server Settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        
        $mail->Username   = 'siriwardanainduwara216@gmail.com'; 
        $mail->Password   = 'farmmgkbnymejzjz'; // Spaces නැතුව 16-digit password එක
        
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // SSL Localhost Fix
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            )
        );

        // Sender & Recipient
        $mail->setFrom('siriwardanainduwara216@gmail.com', 'Salon You');
        $mail->addAddress($to_email, $user_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Salon You - Verification Code';
        $mail->Body    = "Your OTP Verification Code is: <b>{$otp_code}</b>";

        $mail->send();
        return ['status' => true, 'error' => ''];

    } catch (Exception $e) {
        // PHPMailer එකෙන් එන exact error message එක අල්ලාගැනීම
        return ['status' => false, 'error' => $mail->ErrorInfo];
    }
}