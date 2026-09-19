<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require __DIR__ . '/phpmailer/Exception.php';
require __DIR__ . '/phpmailer/SMTP.php';
require __DIR__ . '/phpmailer/PHPMailer.php';

// ============================================================
// Shared helper: builds a PHPMailer instance with the salon's
// SMTP settings already configured. Every email function below
// calls this instead of repeating the SMTP setup each time.
// ============================================================
function get_configured_mailer() {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    $mail->Username   = 'siriwardanainduwara216@gmail.com';
    $mail->Password   = 'farmmgkbnymejzjz'; // without spaces

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

    $mail->setFrom('siriwardanainduwara216@gmail.com', 'Salon You');
    $mail->isHTML(true);

    return $mail;
}

// ============================================================
// OTP verification email (existing - unchanged)
// ============================================================
function send_otp_email($to_email, $user_name, $otp_code) {
    try {
        $mail = get_configured_mailer();
        $mail->addAddress($to_email, $user_name);

        $mail->Subject = 'Salon You - Verification Code';
        $mail->Body    = "Your OTP Verification Code is: <b>{$otp_code}</b>";

        $mail->send();
        return ['status' => true, 'error' => ''];

    } catch (Exception $e) {
        return ['status' => false, 'error' => $mail->ErrorInfo];
    }
}

// ============================================================
// Appointment confirmation email
// Sent when an admin changes an appointment's status to 'confirmed'
// ============================================================
function send_appointment_confirmation_email($to_email, $customer_name, $service_name, $staff_name, $appointment_date, $appointment_time) {
    try {
        $mail = get_configured_mailer();
        $mail->addAddress($to_email, $customer_name);

        $formatted_time = date('h:i A', strtotime($appointment_time));

        $mail->Subject = 'Salon You - Your Appointment is Confirmed!';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto;'>
                <h2 style='color: #d4af37;'>Appointment Confirmed</h2>
                <p>Hi <b>{$customer_name}</b>,</p>
                <p>Your appointment at <b>Salon You</b> has been confirmed. Here are the details:</p>
                <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><b>Service</b></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$service_name}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><b>Stylist</b></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$staff_name}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><b>Date</b></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$appointment_date}</td></tr>
                    <tr><td style='padding: 8px;'><b>Time</b></td><td style='padding: 8px;'>{$formatted_time}</td></tr>
                </table>
                <p style='margin-top: 20px;'>We look forward to seeing you!</p>
                <p style='color: #888; font-size: 12px;'>Salon You - Beauty & Wellness Salon</p>
            </div>
        ";

        $mail->send();
        return ['status' => true, 'error' => ''];

    } catch (Exception $e) {
        return ['status' => false, 'error' => $mail->ErrorInfo];
    }
}

// ============================================================
// Payment receipt email
// Sent when a payment's status is set to 'paid'
// ============================================================
function send_payment_receipt_email($to_email, $customer_name, $service_name, $amount, $payment_method, $payment_date) {
    try {
        $mail = get_configured_mailer();
        $mail->addAddress($to_email, $customer_name);

        $formatted_amount = number_format($amount, 2);
        $formatted_date = date('Y-m-d', strtotime($payment_date));

        $mail->Subject = 'Salon You - Payment Receipt';
        $mail->Body = "
            <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto;'>
                <h2 style='color: #d4af37;'>Payment Receipt</h2>
                <p>Hi <b>{$customer_name}</b>,</p>
                <p>Thank you for your payment. Here is your receipt:</p>
                <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><b>Service</b></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>{$service_name}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><b>Amount Paid</b></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>Rs. {$formatted_amount}</td></tr>
                    <tr><td style='padding: 8px; border-bottom: 1px solid #eee;'><b>Payment Method</b></td><td style='padding: 8px; border-bottom: 1px solid #eee;'>" . ucfirst($payment_method) . "</td></tr>
                    <tr><td style='padding: 8px;'><b>Date</b></td><td style='padding: 8px;'>{$formatted_date}</td></tr>
                </table>
                <p style='margin-top: 20px; color: #22c55e;'><b>Status: Paid</b></p>
                <p style='color: #888; font-size: 12px;'>Thank you for choosing Salon You!</p>
            </div>
        ";

        $mail->send();
        return ['status' => true, 'error' => ''];

    } catch (Exception $e) {
        return ['status' => false, 'error' => $mail->ErrorInfo];
    }
}