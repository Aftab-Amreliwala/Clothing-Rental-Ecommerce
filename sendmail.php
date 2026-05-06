<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';  
    $mail->SMTPAuth   = true;
    $mail->Username   = 'ecommercepro1212@gmail.com';
    $mail->Password   = 'itbe bzrw jfhp dvqu'; // NOT your real password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    // Sender & receiver
    $mail->setFrom('ecommercepro1212@gmail.com', 'Your Name');
    $mail->addAddress('aaftabamreliwala9123@gmail.com');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Mail from PHPMailer';
    $mail->Body    = '<h1>Hello!</h1><p>This is a test email.</p>';

    $mail->send();
    echo 'Message sent successfully';
} catch (Exception $e) {
    echo "Mailer Error: {$mail->ErrorInfo}";
}