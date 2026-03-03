<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer files manually
require __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

function sendMail($to, $subject, $body)
{
    $mail = new PHPMailer(true);

    try {

        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // 🔹 Replace with your project Gmail
        $mail->Username   = 'hostelleavesystem2026@gmail.com';

        // 🔹 Replace with your 16-digit app password (NO SPACES)
        $mail->Password   = 'dqexfjwnhtoyymko';

        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        // Email Settings
        $mail->setFrom('yourprojectemail@gmail.com', 'Hostel Leave System');
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        return true;

    } catch (Exception $e) {
        return false;
    }
}