<?php
// Load PHPMailer files
require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$config = require 'config/config.php';  // Load your config file

function sendPasswordResetEmail($email, $token) {
    global $config;
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = $config['smtp']['host'];                // Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
        $mail->Username   = $config['smtp']['username'];            // SMTP username
        $mail->Password   = $config['smtp']['password'];            // SMTP password
        $mail->SMTPSecure = $config['smtp']['encryption'];          // Enable TLS encryption or SSL
        $mail->Port       = $config['smtp']['port'];                // TCP port to connect to

        // Recipients
        $mail->setFrom($config['smtp']['from_email'], $config['smtp']['from_name']);
        $mail->addAddress($email);                                  // Add recipient

        // Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Password Reset Request';
        $mail->Body    = "
            <h1>Password Reset Request</h1>
            <p>We received a request to reset your password. Click the link below to reset your password:</p>
            <a href='" . $config['app']['url'] . "/resetpassword.php?token=$token'>Reset Password</a>
            <p>If you did not request this, please ignore this email.</p>
        ";
        $mail->AltBody = "We received a request to reset your password. Use this link to reset: " . $config['app']['url'] . "/resetpassword.php?token=$token";

        $mail->send();
        echo 'Password reset email has been sent!';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
