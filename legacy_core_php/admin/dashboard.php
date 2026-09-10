<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include '../config.php';

$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
$total_beauticians = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='beautician'"))['total'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'"))['total'];
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];
$total_reviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM reviews"))['total'];
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status='pending'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 28px 35px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--accent);
        }
        .admin-header h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 4px;
            font-family: 'Playfair Display', serif;
        }
        .admin-header p { font-size: 13px; opacity: 0.8; }
        .admin-date {
            text-align: right;
            font-size: 12px;
            opacity: 0.8;
        }
        .admin-date strong { display: block; font-size: 14px; color: var(--accent); }
        .stats {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        .stat-card {
            background: white;
            padding: 22px 18px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            flex: 1;
            min-width: 130px;
            text-align: center;
            border-bottom: 3px solid var(--accent);
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-hover);
        }
        .stat-card h3 {
            font-size: 36px;
            color: var(--primary);
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }
        .stat-card p {
            color: var(--text-gray);
            font-size: 11px;
            margin-top: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .stat-card.highlight { border-bottom-color: #e65100; }
        .stat-card.highlight h3 { color: #e65100; }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .section {
            background: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 12px;
            border-bottom: 2px solid var(--accent-light);
        }
        .section-header h3 {
            color: var(--primary);
            font-size: 16px;
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }
        .section-header a {
            background: var(--primary);
            color: white;
            padding: 6px 14px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            transition: all 0.2s;
        }
        .section-header a:hover { background: var(--accent); color: var(--primary); }

        @media (max-width: 768px) {
            .grid-2 { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span> — Admin</h1>
    <div>
        <a href="bookings.php">Bookings</a>
        <a href="users.php">Users</a>
        <a href="analytics.php">Analytics</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container" style="max-width: 1100px;">
    <div class="admin-header">
        <div>
            <h2>Admin Dashboard</h2>
            <p>Manage your Beauty Portal platform</p>
        </div>
        <div class="admin-date">
            <strong><?php echo date('d M Y'); ?></strong>
            Logged in as Admin
        </div>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $total_users; ?></h3>
            <p>Total Users</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_customers; ?></h3>
            <p>Customers</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_beauticians; ?></h3>
            <p>Beauticians</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_bookings; ?></h3>
            <p>Total Bookings</p>
        </div>
        <div class="stat-card highlight">
            <h3><?php echo $pending_bookings; ?></h3>
            <p>Pending</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_reviews; ?></h3>
            <p>Reviews</p>
        </div>
    </div>

    <div class="grid-2">
        <div class="section">
            <div class="section-header">
                <h3>Recent Users</h3>
                <a href="users.php">View All</a>
            </div>
            <table>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Joined</th>
                </tr>
                <?php
                $users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC LIMIT 5");
                while ($u = mysqli_fetch_assoc($users)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($u['name']) . "</td>
                        <td><span class='badge " . htmlspecialchars($u['role']) . "'>" . ucfirst($u['role']) . "</span></td>
                        <td>" . date('d M Y', strtotime($u['created_at'])) . "</td>
                    </tr>";
                }
                ?>
            </table>
        </div>

        <div class="section">
            <div class="section-header">
                <h3>Recent Bookings</h3>
                <a href="bookings.php">View All</a>
            </div>
            <table>
                <tr>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Status</th>
                </tr>
                <?php
                $recent_bookings = mysqli_query($conn,
                    "SELECT b.status, u.name as customer_name, s.service_name
                     FROM bookings b
                     JOIN users u ON b.customer_id = u.id
                     JOIN services s ON b.service_id = s.id
                     ORDER BY b.created_at DESC LIMIT 5");
                while ($b = mysqli_fetch_assoc($recent_bookings)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($b['customer_name']) . "</td>
                        <td>" . htmlspecialchars($b['service_name']) . "</td>
                        <td><span class='badge " . htmlspecialchars($b['status']) . "'>" . ucfirst($b['status']) . "</span></td>
                    </tr>";
                }
                ?>
            </table>
        </div>
    </div>
</div>
</body>
</html>