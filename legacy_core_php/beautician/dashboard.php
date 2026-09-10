<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'beautician') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$user_id = $_SESSION['user_id'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     WHERE bp.user_id = '$user_id'"))['total'];

$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     WHERE bp.user_id = '$user_id' AND b.status = 'pending'"))['total'];

$avg_rating = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT AVG(r.rating) as avg, COUNT(r.id) as total 
     FROM reviews r 
     JOIN beautician_profiles bp ON r.beautician_id = bp.id 
     WHERE bp.user_id = '$user_id'"));

$total_services = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM services s
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     WHERE bp.user_id = '$user_id'"))['total'];

$profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bio, location, phone, profile_pic FROM beautician_profiles WHERE user_id='$user_id'"));
$profile_incomplete = !$profile || empty($profile['bio']) || empty($profile['location']) || empty($profile['phone']);

$recent_bookings = mysqli_query($conn,
    "SELECT b.booking_date, b.booking_time, b.status, s.service_name, u.name as customer_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     JOIN users u ON b.customer_id = u.id
     WHERE bp.user_id = '$user_id'
     ORDER BY b.created_at DESC LIMIT 4");

$hour = (int)date('G');
if ($hour < 12) { $greeting = "Good morning"; }
elseif ($hour < 17) { $greeting = "Good afternoon"; }
else { $greeting = "Good evening"; }

$user_name = $_SESSION['user_name'];
$initial = strtoupper(substr($user_name, 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Beautician Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 30px 35px;
            border-radius: var(--border-radius);
            margin-bottom: 22px;
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .welcome-banner::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--accent);
        }
        .welcome-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .welcome-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--accent);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
            flex-shrink: 0;
        }
        .welcome-banner h2 {
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 3px;
            font-family: 'Playfair Display', serif;
        }
        .welcome-banner p { font-size: 13px; opacity: 0.8; }
        .pending-badge {
            background: var(--accent);
            color: var(--primary);
            padding: 10px 18px;
            border-radius: 8px;
            text-align: center;
        }
        .pending-badge .num { font-size: 20px; font-weight: 700; font-family: 'Playfair Display', serif; }
        .pending-badge .lbl { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 2px; }

        .profile-nudge {
            background: #fff3e0;
            color: #e65100;
            padding: 13px 18px;
            border-radius: var(--border-radius);
            margin-bottom: 22px;
            font-size: 13px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-left: 4px solid #e65100;
        }
        .profile-nudge a {
            background: #e65100;
            color: white;
            padding: 6px 16px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
            margin-left: 15px;
        }

        .dashboard-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 22px;
            align-items: start;
        }
        .main-col { min-width: 0; }
        .side-col { min-width: 0; }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 22px;
        }
        .stat-box {
            background: white;
            padding: 18px 14px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            text-align: center;
            border-bottom: 3px solid var(--accent);
        }
        .stat-box .num {
            font-size: 26px;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        .stat-box .lbl {
            font-size: 10px;
            color: var(--text-gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 4px;
            font-weight: 600;
        }
        .stat-box.pending .num { color: #e65100; }
        .stat-box.rating .num { color: #b8935a; }

        .panel {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 22px 24px;
            margin-bottom: 20px;
        }
        .panel-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-light);
        }

        .action-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .action-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px;
            border: 1px solid #eee;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .action-row:hover {
            border-color: var(--accent);
            background: #fafafa;
        }
        .action-row .info h4 {
            color: var(--primary);
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 2px;
        }
        .action-row .info p {
            color: var(--text-gray);
            font-size: 12px;
        }
        .action-row .arrow {
            color: var(--accent);
            font-size: 18px;
        }

        .side-panel {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            padding: 20px 22px;
            margin-bottom: 20px;
        }
        .side-panel-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--accent-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .side-panel-title a {
            font-size: 11px;
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            text-transform: uppercase;
        }
        .recent-item {
            padding: 10px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .recent-item:last-child { border-bottom: none; padding-bottom: 0; }
        .recent-item h4 {
            font-size: 12.5px;
            color: var(--text-dark);
            font-weight: 600;
            margin-bottom: 2px;
        }
        .recent-item p {
            font-size: 11px;
            color: var(--text-gray);
            margin-bottom: 5px;
        }
        .no-bookings-msg {
            text-align: center;
            color: var(--text-gray);
            font-size: 12px;
            padding: 15px 0;
        }

        .rating-summary {
            text-align: center;
            padding: 6px 0 10px;
        }
        .rating-summary .big-num {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        .rating-summary .stars { color: #f4c430; font-size: 15px; margin: 4px 0; }
        .rating-summary p { font-size: 11px; color: var(--text-gray); }

        @media (max-width: 900px) {
            .dashboard-layout { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .welcome-banner { flex-direction: column; align-items: flex-start; gap: 14px; }
            .profile-nudge { flex-direction: column; align-items: flex-start; gap: 10px; }
            .profile-nudge a { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span></h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <?php include '../notification_bell.php'; ?>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container" style="max-width: 1100px;">
    <div class="welcome-banner">
        <div class="welcome-left">
            <div class="welcome-avatar"><?php echo htmlspecialchars($initial); ?></div>
            <div>
                <h2><?php echo htmlspecialchars($greeting); ?>, <?php echo htmlspecialchars($user_name); ?></h2>
                <p>Manage your beauty business from here</p>
            </div>
        </div>
        <?php if($pending_bookings > 0): ?>
        <div class="pending-badge">
            <div class="num"><?php echo $pending_bookings; ?></div>
            <div class="lbl">Pending</div>
        </div>
        <?php endif; ?>
    </div>

    <?php if($profile_incomplete): ?>
    <div class="profile-nudge">
        <span>Your profile isn't complete yet. Add your bio, location and phone so customers can find and trust you.</span>
        <a href="profile.php">Complete Profile</a>
    </div>
    <?php endif; ?>

    <div class="stats-row">
        <div class="stat-box">
            <div class="num"><?php echo $total_bookings; ?></div>
            <div class="lbl">Total Bookings</div>
        </div>
        <div class="stat-box pending">
            <div class="num"><?php echo $pending_bookings; ?></div>
            <div class="lbl">Pending</div>
        </div>
        <div class="stat-box rating">
            <div class="num"><?php echo $avg_rating['total'] > 0 ? number_format($avg_rating['avg'], 1) : 'N/A'; ?></div>
            <div class="lbl">Avg Rating</div>
        </div>
        <div class="stat-box">
            <div class="num"><?php echo $total_services; ?></div>
            <div class="lbl">My Services</div>
        </div>
    </div>

    <div class="dashboard-layout">
        <div class="main-col">
            <div class="panel">
                <div class="panel-title">Quick Actions</div>
                <div class="action-list">
                    <a href="profile.php" class="action-row">
                        <div class="info">
                            <h4>My Profile</h4>
                            <p>Update your profile and bio</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="services.php" class="action-row">
                        <div class="info">
                            <h4>My Services</h4>
                            <p>Add or edit your service listings</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="portfolio.php" class="action-row">
                        <div class="info">
                            <h4>My Portfolio</h4>
                            <p>Showcase photos of your best work</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="availability.php" class="action-row">
                        <div class="info">
                            <h4>Availability</h4>
                            <p>Set your available days and times</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="bookings.php" class="action-row">
                        <div class="info">
                            <h4>Bookings</h4>
                            <p>View and manage appointments</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="side-col">
            <div class="side-panel">
                <div class="side-panel-title">
                    Recent Appointments
                    <a href="bookings.php">View All</a>
                </div>
                <?php if(mysqli_num_rows($recent_bookings) > 0): ?>
                    <?php while($r = mysqli_fetch_assoc($recent_bookings)): ?>
                    <div class="recent-item">
                        <h4><?php echo htmlspecialchars($r['service_name']); ?></h4>
                        <p>with <?php echo htmlspecialchars($r['customer_name']); ?> · <?php echo date('d M', strtotime($r['booking_date'])); ?></p>
                        <span class="badge <?php echo htmlspecialchars($r['status']); ?>"><?php echo ucfirst($r['status']); ?></span>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                <div class="no-bookings-msg">No appointments yet.<br>Share your profile to get bookings.</div>
                <?php endif; ?>
            </div>

            <div class="side-panel">
                <div class="side-panel-title">Your Rating</div>
                <div class="rating-summary">
                    <div class="big-num"><?php echo $avg_rating['total'] > 0 ? number_format($avg_rating['avg'], 1) : '—'; ?></div>
                    <?php if($avg_rating['total'] > 0): ?>
                    <div class="stars">★★★★★</div>
                    <p><?php echo $avg_rating['total']; ?> review<?php echo $avg_rating['total'] != 1 ? 's' : ''; ?></p>
                    <?php else: ?>
                    <p>No reviews yet</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>