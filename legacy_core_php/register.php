<?php
include 'config.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email already registered.";
    } else {
        $sql = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        if (mysqli_query($conn, $sql)) {
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Beauty Portal</title>
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
        .perks { margin-top: 35px; }
        .perk-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
        }
        .perk-dot {
            width: 6px;
            height: 6px;
            background: var(--accent);
            border-radius: 50%;
            margin-top: 7px;
            flex-shrink: 0;
        }
        .perk-text { font-size: 13px; opacity: 0.85; line-height: 1.6; }

        .right-panel {
            width: 460px;
            background: var(--light-bg);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow-y: auto;
        }
        .form-box { width: 100%; max-width: 360px; padding: 0; box-shadow: none; background: none; }
        .form-box h2 {
            font-size: 24px;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
            color: var(--primary);
        }
        .subtitle { color: var(--text-gray); font-size: 13px; margin-bottom: 25px; }
        .login-link { text-align: center; margin-top: 20px; font-size: 13px; color: var(--text-gray); }
        .login-link a {
            color: var(--primary);
            font-weight: 600;
            text-decoration: none;
            border-bottom: 1px solid var(--accent);
        }
        .role-options { display: flex; gap: 15px; margin-bottom: 16px; }
        .role-options label {
            flex: 1;
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: var(--text-gray);
            background: white;
        }
        .role-options input[type="radio"] { display: none; }
        .role-options label:has(input:checked) {
            border-color: var(--accent);
            background: var(--accent-light);
            color: var(--primary);
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
    <h2>Join Gilgit's growing Beauty community.</h2>
    <p>Whether you're looking for services or offering them, Beauty Portal connects you with the right people.</p>

    <div class="perks">
        <div class="perk-item">
            <div class="perk-dot"></div>
            <div class="perk-text">Browse verified beauticians by location, price, and rating</div>
        </div>
        <div class="perk-item">
            <div class="perk-dot"></div>
            <div class="perk-text">Book appointments instantly with real-time availability</div>
        </div>
        </div>
    </div>
</div>

<div class="right-panel">
    <div class="form-box">
        <h2>Create Account</h2>
        <p class="subtitle">Join Beauty Portal today</p>

        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>

        <form method="POST">
            <label>Full Name</label>
            <input type="text" name="name" required placeholder="Enter your full name">

            <label>Email Address</label>
            <input type="email" name="email" required placeholder="Enter your email">

            <label>Password</label>
            <input type="password" name="password" required placeholder="Create a strong password">

            <label>Register As</label>
            <div class="role-options">
                <label>
                    <input type="radio" name="role" value="customer" required>
                    Customer
                </label>
                <label>
                    <input type="radio" name="role" value="beautician">
                    Beautician
                </label>
            </div>

            <button type="submit">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</div>
</body>
</html>