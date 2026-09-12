<?php
session_start();
include '../config.php';

$error = "";
$MAX_ATTEMPTS = 5;
$LOCK_MINUTES = 5;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        $is_locked = !empty($user['locked_until']) && strtotime($user['locked_until']) > time();

        if ($is_locked) {
            $minutes_left = ceil((strtotime($user['locked_until']) - time()) / 60);
            $error = "Too many failed attempts. Please try again in " . $minutes_left . " minute(s).";
        } elseif (password_verify($password, $user['password'])) {

            if ($user['role'] != 'admin') {
                $error = "Incorrect password.";
            } elseif (isset($user['is_active']) && $user['is_active'] == 0) {
                $error = "This account has been deactivated.";
            } else {
                mysqli_query($conn, "UPDATE users SET failed_attempts=0, locked_until=NULL WHERE id='{$user['id']}'");

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: dashboard.php");
                exit();
            }
        } else {
            $new_attempts = $user['failed_attempts'] + 1;

            if ($new_attempts >= $MAX_ATTEMPTS) {
                $lock_until = date('Y-m-d H:i:s', strtotime("+$LOCK_MINUTES minutes"));
                mysqli_query($conn, "UPDATE users SET failed_attempts=$new_attempts, locked_until='$lock_until' WHERE id='{$user['id']}'");
                $error = "Too many failed attempts. Account locked for $LOCK_MINUTES minutes.";
            } else {
                mysqli_query($conn, "UPDATE users SET failed_attempts=$new_attempts WHERE id='{$user['id']}'");
                $error = "Incorrect password.";
            }
        }
    } else {
        $error = "Incorrect password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Access</title>
    <link rel="stylesheet" href="../style.css">
    <meta name="robots" content="noindex, nofollow">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--primary);
        }
        .admin-login-box {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            max-width: 380px;
            width: 100%;
        }
        .admin-login-box h2 {
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
            text-align: center;
        }
        .admin-login-box .subtitle {
            text-align: center;
            color: var(--text-gray);
            font-size: 12px;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>
    <div class="admin-login-box">
        <h2>Administrator Access</h2>
        <p class="subtitle">BookMyBeauty internal login</p>

        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>