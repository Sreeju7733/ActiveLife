<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$mail = new PHPMailer(true);

try {
    // Gmail SMTP configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sree.gowtham.v@gmail.com'; // Your Gmail address
    $mail->Password   = 'crdz gggj lbtf ulic';   // Replace with your Gmail App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Sender and recipient
    $mail->setFrom('sree.gowtham.v@gmail.com', 'Sree Gowtham');
    $mail->addAddress('sreeju.programmer@gmail.com', 'Sreeju'); // Update with real recipient

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Gmail';
    $mail->Body    = 'Hey';
    $mail->AltBody = 'Hello, this is a test email sent via Gmail SMTP using PHPMailer (plain text version).';

    $mail->send();
    echo '✅ Email sent successfully.';
} catch (Exception $e) {
    echo "❌ Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
