<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$customer_id = $_SESSION['user_id'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM bookings WHERE customer_id='$customer_id'"))['total'];
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM bookings WHERE customer_id='$customer_id' AND status='pending'"))['total'];
$confirmed_bookings = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM bookings WHERE customer_id='$customer_id' AND status='confirmed'"))['total'];
$favorites_count = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT COUNT(*) as total FROM favorites WHERE customer_id='$customer_id'"))['total'];

$recent = mysqli_query($conn,
    "SELECT b.booking_date, b.booking_time, b.status, s.service_name, u.name as beautician_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     JOIN users u ON bp.user_id = u.id
     WHERE b.customer_id = '$customer_id'
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
    <title>Customer Dashboard</title>
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
        .welcome-banner .date-badge {
            font-size: 12px;
            opacity: 0.8;
            text-align: right;
        }
        .welcome-banner .date-badge strong {
            display: block;
            font-size: 16px;
            color: var(--accent);
            font-family: 'Playfair Display', serif;
        }

        /* Two-column dashboard layout */
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
        .stat-box.confirmed .num { color: #2e7d32; }

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
            border: 1px solid var(--border);
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.2s;
        }
        .action-row:hover {
            border-color: var(--accent);
            background: var(--light-bg);
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

        /* Sidebar */
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
        .no-bookings-msg a { color: var(--accent); text-decoration: none; font-weight: 600; }

        .favorites-nudge {
            background: var(--light-bg);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }
        .favorites-nudge .num {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        .favorites-nudge p {
            font-size: 11px;
            color: var(--text-gray);
            margin: 4px 0 10px;
        }
        .favorites-nudge a {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 7px 16px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .favorites-nudge a:hover { background: var(--accent); color: var(--primary); }

        @media (max-width: 900px) {
            .dashboard-layout { grid-template-columns: 1fr; }
            .stats-row { grid-template-columns: repeat(2, 1fr); }
            .welcome-banner { flex-direction: column; align-items: flex-start; gap: 14px; }
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
                <p>Here's your beauty journey at a glance</p>
            </div>
        </div>
        <div class="date-badge">
            <strong><?php echo date('d M'); ?></strong>
            <?php echo date('Y'); ?>
        </div>
    </div>

    <div class="stats-row">
        <div class="stat-box">
            <div class="num"><?php echo $total_bookings; ?></div>
            <div class="lbl">Total Bookings</div>
        </div>
        <div class="stat-box pending">
            <div class="num"><?php echo $pending_bookings; ?></div>
            <div class="lbl">Pending</div>
        </div>
        <div class="stat-box confirmed">
            <div class="num"><?php echo $confirmed_bookings; ?></div>
            <div class="lbl">Confirmed</div>
        </div>
        <div class="stat-box">
            <div class="num"><?php echo $favorites_count; ?></div>
            <div class="lbl">Favorites</div>
        </div>
    </div>

    <div class="dashboard-layout">
        <div class="main-col">
            <div class="panel">
                <div class="panel-title">Quick Actions</div>
                <div class="action-list">
                    <a href="browse.php" class="action-row">
                        <div class="info">
                            <h4>Find Beauticians</h4>
                            <p>Browse by location, service, price and rating</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="my_bookings.php" class="action-row">
                        <div class="info">
                            <h4>My Bookings</h4>
                            <p>View, track and manage your appointments</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="favorites.php" class="action-row">
                        <div class="info">
                            <h4>My Favorites</h4>
                            <p>Beauticians you've saved for later</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                    <a href="browse.php" class="action-row">
                        <div class="info">
                            <h4>Leave a Review</h4>
                            <p>Rate your experience and help others</p>
                        </div>
                        <span class="arrow">→</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="side-col">
            <div class="side-panel">
                <div class="side-panel-title">
                    Recent Bookings
                    <a href="my_bookings.php">View All</a>
                </div>
                <?php if(mysqli_num_rows($recent) > 0): ?>
                    <?php while($r = mysqli_fetch_assoc($recent)): ?>
                    <div class="recent-item">
                        <h4><?php echo htmlspecialchars($r['service_name']); ?></h4>
                        <p>with <?php echo htmlspecialchars($r['beautician_name']); ?> · <?php echo date('d M', strtotime($r['booking_date'])); ?></p>
                        <span class="badge <?php echo htmlspecialchars($r['status']); ?>"><?php echo ucfirst($r['status']); ?></span>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                <div class="no-bookings-msg">No bookings yet.<br><a href="browse.php">Book your first appointment</a></div>
                <?php endif; ?>
            </div>

            <div class="side-panel">
                <div class="side-panel-title">Favorites</div>
                <div class="favorites-nudge">
                    <div class="num"><?php echo $favorites_count; ?></div>
                    <p>beautician<?php echo $favorites_count != 1 ? 's' : ''; ?> saved</p>
                    <a href="favorites.php">View List</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../chatbot_widget.php'; ?>
</body>
</html>