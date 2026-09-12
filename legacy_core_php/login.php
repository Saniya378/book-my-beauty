<?php
session_start();
include 'config.php';

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
            $error = "Too many failed attempts. Please try again in " . $minutes_left . " minute(s), or reset your password.";
        } elseif (password_verify($password, $user['password'])) {

            // This public login page never allows admin accounts to sign in here,
            // regardless of whether the password is correct. Admin access uses a
            // separate, unlisted login route. We show the same generic message as
            // a wrong password so this page never reveals that an admin account exists.
            if ($user['role'] == 'admin') {
                $error = "Incorrect password.";
            } elseif (isset($user['is_active']) && $user['is_active'] == 0) {
                $error = "This account has been deactivated. Please contact support.";
            } else {
                mysqli_query($conn, "UPDATE users SET failed_attempts=0, locked_until=NULL WHERE id='{$user['id']}'");

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] == 'beautician') {
                    header("Location: beautician/dashboard.php");
                } else {
                    header("Location: customer/dashboard.php");
                }
                exit();
            }
        } else {
            $new_attempts = $user['failed_attempts'] + 1;

            if ($new_attempts >= $MAX_ATTEMPTS) {
                $lock_until = date('Y-m-d H:i:s', strtotime("+$LOCK_MINUTES minutes"));
                mysqli_query($conn, "UPDATE users SET failed_attempts=$new_attempts, locked_until='$lock_until' WHERE id='{$user['id']}'");
                $error = "Too many failed attempts. Your account is locked for $LOCK_MINUTES minutes.";
            } else {
                mysqli_query($conn, "UPDATE users SET failed_attempts=$new_attempts WHERE id='{$user['id']}'");
                $remaining = $MAX_ATTEMPTS - $new_attempts;
                $error = "Incorrect password. $remaining attempt(s) remaining before your account is temporarily locked.";
            }
        }
    } else {
        $error = "Email not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - BookMyBeauty</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background: var(--primary);
        }
        .left-panel {
    flex: 1;
    background: linear-gradient(rgba(26,26,46,0.92), rgba(15,52,96,0.95)), url('images/image.jpg') center/cover no-repeat;
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: 60px;
    color: white;
}
        .left-panel .logo-mark {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }
        .left-panel .logo-mark em { color: var(--accent); font-style: normal; }
        .left-panel h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            line-height: 1.3;
            margin-bottom: 15px;
        }
        .left-panel p { font-size: 14px; opacity: 0.75; line-height: 1.7; max-width: 380px; }
        .demo-accounts {
            margin-top: 40px;
            display: flex;
            gap: 12px;
        }
        .demo-card {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.15);
            border-radius: 8px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            flex: 1;
        }
        .demo-card:hover { background: rgba(201,169,110,0.2); border-color: var(--accent); }
        .demo-card .name {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--accent);
        }
        .demo-card .desc { font-size: 10px; opacity: 0.6; margin-top: 3px; }

        .right-panel {
            width: 460px;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .form-box { width: 100%; max-width: 360px; padding: 0; box-shadow: none; background: none; }
        .form-box h2 {
            font-size: 24px;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
            color: var(--primary);
        }
        .subtitle { color: var(--text-gray); font-size: 13px; margin-bottom: 30px; }
        .login-link { text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-gray); }
        .login-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px solid var(--accent);
        }

        @media (max-width: 850px) {
            .left-panel { display: none; }
            .right-panel { width: 100%; }
        }
    </style>
</head>
<body>

<div class="left-panel">
     <a href="index.php" class="back-link">← Back to Home</a>
    <div class="logo-mark">Beauty <em>Portal</em></div>
    <h2>Where beauty meets convenience.</h2>
    <p>Connect with verified beauticians, book appointments instantly, and manage your beauty journey — all in one place.</p>

    <div class="demo-accounts">
        <div class="demo-card" onclick="fillLogin('test@test.com', '123456')">
            <div class="name">Customer</div>
            <div class="desc">Try as customer</div>
        </div>
        <div class="demo-card" onclick="fillLogin('beautician@test.com', '123456')">
            <div class="name">Beautician</div>
            <div class="desc">Try as beautician</div>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="form-box">
       
        <h2>Welcome Back</h2>
        <p class="subtitle">Login to continue to your account</p>

        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="POST">
            <label>Email Address</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email">

            <label>Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">

            <div style="text-align: right; margin-top: -10px; margin-bottom: 16px;">
                <a href="forgot_password.php" style="font-size: 12px; color: var(--text-gray); text-decoration: none;">Forgot Password?</a>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="login-link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</div>

<script>
function fillLogin(email, password) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = password;
}
</script>
</body>
</html>