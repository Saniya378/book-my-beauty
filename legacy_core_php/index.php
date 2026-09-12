<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_role'] == 'customer') {
        header("Location: customer/dashboard.php");
    } elseif ($_SESSION['user_role'] == 'beautician') {
        header("Location: beautician/dashboard.php");
    } elseif ($_SESSION['user_role'] == 'admin') {
        header("Location: admin/dashboard.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>BookMyBeauty</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .stats-strip {
            background: white;
            display: flex;
            justify-content: center;
            gap: 60px;
            padding: 40px 20px;
            flex-wrap: wrap;
            border-bottom: 1px solid #f0f0f0;
        }
        .stat-item { text-align: center; }
        .stat-item .number {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        .stat-item .label {
            font-size: 11px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }

        .how-it-works {
            padding: 80px 40px;
            background: var(--light-bg);
        }
        .section-heading {
            text-align: center;
            margin-bottom: 50px;
        }
        .section-heading h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 10px;
        }
        .section-heading p {
            color: var(--text-gray);
            font-size: 14px;
        }
        .steps {
            display: flex;
            justify-content: center;
            gap: 40px;
            max-width: 1000px;
            margin: 0 auto;
            flex-wrap: wrap;
        }
        .step {
            text-align: center;
            flex: 1;
            min-width: 220px;
            position: relative;
        }
        .step-number {
            width: 50px;
            height: 50px;
            background: var(--primary);
            color: var(--accent);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            margin: 0 auto 20px;
        }
        .step h4 {
            color: var(--primary);
            font-size: 16px;
            margin-bottom: 10px;
            font-family: 'Playfair Display', serif;
        }
        .step p {
            color: var(--text-gray);
            font-size: 13px;
            line-height: 1.7;
        }
    </style>
</head>
<body>

<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span></h1>
    <div>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div>
</div>

<div class="hero">
    <div class="hero-content">
        <h2>Book <span>Beauty</span> Services Online</h2>
        <p>Connect with skilled, verified beauticians in your area. Simple booking, no waiting, no hassle.</p>
        <div class="hero-btns">
            <a href="register.php" class="btn-white">Get Started</a>
            <a href="login.php" class="btn-outline">Login</a>
        </div>
    </div>
</div>

<div class="stats-strip">
    <div class="stat-item">
        <div class="number">50+</div>
        <div class="label">Beauticians</div>
    </div>
    <div class="stat-item">
        <div class="number">200+</div>
        <div class="label">Bookings Made</div>
    </div>
    <div class="stat-item">
        <div class="number">4.8</div>
        <div class="label">Average Rating</div>
    </div>
    <div class="stat-item">
        <div class="number">3</div>
        <div class="label">Cities Covered</div>
    </div>
</div>

<div class="features">
    <div class="feature">
        <div class="icon">🔍</div>
        
        <a href="customer/browse.php" style="text-decoration:none">Find Beauticians</a>
        <p>Browse verified beauticians by location, service type, price range, and ratings.</p>
    </div>
    <div class="feature">
        <div class="icon">📅</div>
        <h3>Easy Booking</h3>
        <p>Book appointments online in minutes with real-time availability checking.</p>
    </div>
    <div class="feature">
        <div class="icon">💼</div>
        <h3>For Beauticians</h3>
        <p>Showcase your services, manage your schedule, and grow your client base.</p>
    </div>
</div>

<div class="how-it-works">
    <div class="section-heading">
        <h2>How It Works</h2>
        <p>Book your beauty appointment in three simple steps</p>
    </div>
    <div class="steps">
        <div class="step">
            <div class="step-number">1</div>
            <h4>Search And Compare</h4>
            <p>Browse beauticians by location, service, price, and customer ratings.</p>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <h4>Book Your Slot</h4>
            <p>Choose your preferred service, date, and time based on availability.</p>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <h4>Get Confirmed</h4>
            <p>Receive instant confirmation and pay in person at your appointment.</p>
        </div>
    </div>
</div>

<div class="cta">
    <h2>Are you a beautician?</h2>
    <p>Join our platform and reach more customers today.</p>
    <a href="register.php">Register as Beautician</a>
</div>

<footer>
    &copy; 2026 Beauty <span>Portal</span> — KIU Final Year Project
</footer>
<?php include 'chatbot_widget.php'; ?>
</body>
</html>