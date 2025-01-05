<?php
/**
 * File Name: mail.php
 * Author: Eliasaf Yakar, Niv Zukerman and Nissan Yaar
 * Created On: 2025-01-01
 * Last Modified: 2025-01-01

 * This PHP script handles email submissions using the PHPMailer library. Users can submit their name, email, subject, and message via a form. The script configures a Gmail SMTP connection to send the submitted email to a predefined recipient.

 * Features:
 * - **PHPMailer Integration**:
 *   - Uses the PHPMailer library for robust email handling.
 *   - Supports Gmail's SMTP server with STARTTLS encryption.
 * - **User Input Handling**:
 *   - Collects the sender's name, email, subject, and message body from `POST` data.
 *   - Combines these inputs into the email content.
 * - **UTF-8 Encoding**:
 *   - Ensures proper encoding for multilingual support.
 * - **Error Handling**:
 *   - Catches and displays detailed errors if email sending fails.
 * - **User Feedback**:
 *   - Provides success and error alerts via `alert()` in JavaScript.
 *   - Redirects users to appropriate pages (`index.php` on success, `contact.php` on failure).
 */
require 'vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// define phpmailer object
$mail = new PHPMailer(true);

// define the mail details we get with POST method from the front
$name = $_POST["name"];
$userEmail = $_POST["email"]; 
$subject = $_POST["subject"];
$body = $_POST["body"];

try {
    // gmail smtp configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'eliyakar178@gmail.com'; 
    $mail->Password = 'bzsm meca lxga uvbg';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // define sender and recipient  
    $mail->setFrom($userEmail);
    $mail->addAddress('eliyakar178@gmail.com');    

    // mail content
    $mail->CharSet = 'UTF-8';
    $mail->isHTML(false);
    $mail->Subject = $subject;
    $mail->Body = $name . "\n" . $userEmail . "\n\n" . $body;

    // send the mail 
    $mail->send();
    $message =  "ההודעה נשלחה בהצלחה!";
  echo "<script>
            alert('$message');
            window.location.href = 'index.php';
          </script>";
    exit();

} catch (Exception $e) {
    $message = "שגיאה בהשליחה: {$mail->ErrorInfo}";
    echo "<script>
            alert('$message');
            window.location.href = 'contact.php';
          </script>";
    exit(); 
}
?>
