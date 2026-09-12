<?php
session_start();
include 'config.php';

$token = isset($_GET['token']) ? $_GET['token'] : (isset($_POST['token']) ? $_POST['token'] : '');
$error = "";
$success = "";

$valid = false;
$user = null;

if (!empty($token)) {
    $safe_token = mysqli_real_escape_string($conn, $token);
    $user = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT id, reset_expires FROM users WHERE reset_token='$safe_token'"));

    if ($user && strtotime($user['reset_expires']) > time()) {
        $valid = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && $valid) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE users SET password='$hashed', reset_token=NULL, reset_expires=NULL WHERE id='{$user['id']}'");
        $success = "Password updated successfully! You can now log in.";
        $valid = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - BookMyBeauty</title>
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
        <h2>Reset Password</h2>

        <?php if($success): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
            <div class="login-link"><a href="login.php">Go to Login</a></div>
        <?php elseif(!$valid): ?>
            <p class="error">This reset link is invalid or has expired. Please request a new one.</p>
            <div class="login-link"><a href="forgot_password.php">Request New Link</a></div>
        <?php else: ?>
            <?php if($error): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <label>New Password</label>
                <input type="password" name="password" required placeholder="At least 6 characters">
                <label>Confirm New Password</label>
                <input type="password" name="confirm_password" required placeholder="Re-enter new password">
                <button type="submit">Update Password</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>