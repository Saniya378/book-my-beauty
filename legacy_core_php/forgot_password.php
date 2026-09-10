<?php
session_start();
include 'config.php';
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));

    $user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, name FROM users WHERE email='$email'"));

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        mysqli_query($conn, "UPDATE users SET reset_token='$token', reset_expires='$expires' WHERE id='{$user['id']}'");

        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reset_password.php?token=" . $token;

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_EMAIL;
            $mail->Password = SMTP_APP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom(SMTP_EMAIL, SMTP_FROM_NAME);
            $mail->addAddress($email, $user['name']);

            $mail->isHTML(true);
            $mail->Subject = 'Reset your Beauty Portal password';
            $mail->Body = "Hi " . htmlspecialchars($user['name']) . ",<br><br>"
                . "We received a request to reset your Beauty Portal password. Click the link below to set a new one (valid for 1 hour):<br><br>"
                . "<a href='" . $reset_link . "'>" . $reset_link . "</a><br><br>"
                . "If you didn't request this, you can safely ignore this email.";
            $mail->AltBody = "Reset your password using this link (valid 1 hour): " . $reset_link;

            $mail->send();
            $message = "A password reset link has been sent to your email. Please check your inbox (and spam folder).";
        } catch (Exception $e) {
            $error = "Couldn't send the reset email. (" . $mail->ErrorInfo . ")";
        }
    } else {
        // Same message either way — don't reveal whether an email is registered
        $message = "If that email is registered with us, a password reset link has been sent to it.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - Beauty Portal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--primary);
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Forgot Password</h2>
        <p class="subtitle" style="text-align:center; color:var(--text-gray); font-size:13px; margin-bottom:20px;">Enter your email to reset your password</p>

        <?php if($message): ?>
        <p class="success"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if(!$message): ?>
        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" required placeholder="Enter your registered email">
            <button type="submit">Send Reset Link</button>
        </form>
        <?php else: ?>
        <form method="POST">
            <label>Try a different email</label>
            <input type="email" name="email" required placeholder="Enter your registered email">
            <button type="submit">Send Reset Link</button>
        </form>
        <?php endif; ?>

        <div class="login-link">
            <a href="login.php">← Back to Login</a>
        </div>
    </div>
</body>
</html>