<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}
include '../config.php';

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['id'])) {
    $bid = (int)$_POST['id'];
    $action = $_POST['action'] === 'confirm' ? 'confirmed' : ($_POST['action'] === 'cancel' ? 'cancelled' : '');

    if (!empty($action)) {
        if (mysqli_query($conn, "UPDATE bookings SET status='$action' WHERE id='$bid'")) {
            $success_msg = "Booking " . $action . " successfully.";
            $binfo = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT b.customer_id, s.service_name FROM bookings b 
                 JOIN services s ON b.service_id = s.id WHERE b.id='$bid'"));
            if ($binfo) {
                $smsg = mysqli_real_escape_string($conn, "Your {$binfo['service_name']} booking has been $action by Admin");
                mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ('{$binfo['customer_id']}', '$smsg')");
            }
        } else {
            $error_msg = "Failed to update booking.";
        }
    }
}

// Filters
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT b.id, b.booking_date, b.booking_time, b.status,
        s.service_name, s.price,
        u1.name as customer_name,
        u2.name as beautician_name
        FROM bookings b
        JOIN services s ON b.service_id = s.id
        JOIN users u1 ON b.customer_id = u1.id
        JOIN beautician_profiles bp ON s.beautician_id = bp.id
        JOIN users u2 ON bp.user_id = u2.id
        WHERE 1=1";

if ($status_filter) $sql .= " AND b.status='$status_filter'";
if ($search) $sql .= " AND (u1.name LIKE '%$search%' OR u2.name LIKE '%$search%' OR s.service_name LIKE '%$search%')";

// Count total matches (before LIMIT) for pagination — wrap as subquery so it
// always works regardless of the exact SELECT columns used above.
$count_sql = "SELECT COUNT(*) as total FROM (" . $sql . ") as count_wrapper";
$count_result = mysqli_query($conn, $count_sql);
$total = $count_result ? mysqli_fetch_assoc($count_result)['total'] : 0;

$per_page = 10;
$total_pages = max(1, ceil($total / $per_page));
$current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$current_page = min($current_page, $total_pages);
$offset = ($current_page - 1) * $per_page;

$sql .= " ORDER BY b.booking_date DESC LIMIT $per_page OFFSET $offset";

$bookings = mysqli_query($conn, $sql);

// Counts for filter badges
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='pending'"))['c'];
$confirmed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='confirmed'"))['c'];
$cancelled_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM bookings WHERE status='cancelled'"))['c'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Bookings - Admin</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 3px solid var(--accent);
        }
        .page-header h2 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
            font-family: 'Playfair Display', serif;
        }
        .page-header p { font-size: 13px; opacity: 0.8; }
        .total-badge {
            background: var(--accent);
            color: var(--primary);
            padding: 8px 18px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
        }
        .filter-box {
            background: white;
            padding: 18px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
            border-top: 3px solid var(--accent);
        }
        .filter-box input, .filter-box select {
            flex: 1;
            margin-bottom: 0;
            min-width: 150px;
        }
        .filter-box button { width: auto !important; padding: 12px 25px; }
        .status-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .status-tab {
            padding: 8px 16px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 2px solid var(--border);
            color: var(--text-gray);
            transition: all 0.2s;
        }
        .status-tab:hover, .status-tab.active {
            border-color: var(--primary);
            background: var(--primary);
            color: white;
        }
        .status-tab .count {
            background: rgba(255,255,255,0.2);
            padding: 2px 7px;
            border-radius: 10px;
            margin-left: 5px;
            font-size: 11px;
        }
        .inline-action-form { display: inline; }
        .btn-confirm {
            width: auto !important;
            display: inline-block !important;
            width: auto !important;
            display: inline-block !important;
            padding: 5px 12px !important;
            background: #e8f5e9;
            color: #2e7d32;
            border: none;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
            margin-right: 5px;
            margin-bottom: 0;
            transition: all 0.2s;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .btn-confirm:hover { background: #2e7d32; color: white; }
        .btn-cancel-action {
            width: auto !important;
            display: inline-block !important;
            width: auto !important;
            display: inline-block !important;
            padding: 5px 12px !important;
            background: #fce4ec;
            color: #c62828;
            border: none;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 700;
            margin-bottom: 0;
            transition: all 0.2s;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .btn-cancel-action:hover { background: #c62828; color: white; }
        .alert {
            padding: 12px 18px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 13px;
            border-left: 4px solid;
        }
        .alert-success { background: #e8f5e9; color: #2e7d32; border-color: #2e7d32; }
        .alert-error { background: #fce4ec; color: #c62828; border-color: #c62828; }
        .no-data {
            background: white;
            padding: 50px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            box-shadow: var(--shadow);
        }
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            display: inline-block;
            padding: 8px 14px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            border: 1.5px solid var(--border);
            color: var(--text-gray);
        }
        .pagination a:hover {
            border-color: var(--accent);
            color: var(--primary);
        }
        .pagination .active-page {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .pagination .disabled {
            opacity: 0.4;
            pointer-events: none;
        }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span> — Admin</h1>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <a href="users.php">Users</a>
        <a href="analytics.php">Analytics</a>
        <a href="../logout.php">Logout</a>
    </div>
</div>

<div class="container" style="max-width: 1100px;">
    <div class="page-header">
        <div>
            <h2>All Bookings</h2>
            <p>Manage and oversee all platform appointments</p>
        </div>
        <div class="total-badge"><?php echo $total; ?> Bookings</div>
    </div>

    <?php if($success_msg): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success_msg); ?></div>
    <?php endif; ?>
    <?php if($error_msg): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
    <?php endif; ?>

    <div class="status-tabs">
        <a href="bookings.php" class="status-tab <?php echo !$status_filter ? 'active' : ''; ?>">
            All <span class="count"><?php echo $pending_count + $confirmed_count + $cancelled_count; ?></span>
        </a>
        <a href="bookings.php?status=pending" class="status-tab <?php echo $status_filter=='pending' ? 'active' : ''; ?>">
            Pending <span class="count"><?php echo $pending_count; ?></span>
        </a>
        <a href="bookings.php?status=confirmed" class="status-tab <?php echo $status_filter=='confirmed' ? 'active' : ''; ?>">
            Confirmed <span class="count"><?php echo $confirmed_count; ?></span>
        </a>
        <a href="bookings.php?status=cancelled" class="status-tab <?php echo $status_filter=='cancelled' ? 'active' : ''; ?>">
            Cancelled <span class="count"><?php echo $cancelled_count; ?></span>
        </a>
    </div>

    <form method="GET" class="filter-box">
        <?php if($status_filter): ?>
        <input type="hidden" name="status" value="<?php echo htmlspecialchars($status_filter); ?>">
        <?php endif; ?>
        <input type="text" name="search" placeholder="Search customer, beautician, service..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
        <?php if($search): ?>
        <a href="bookings.php<?php echo $status_filter ? '?status='.$status_filter : ''; ?>" style="color:var(--text-gray); font-size:13px; text-decoration:none; padding:12px;">Clear</a>
        <?php endif; ?>
    </form>

    <?php if($total > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Beautician</th>
            <th>Service</th>
            <th>Price</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
        <?php while($b = mysqli_fetch_assoc($bookings)): ?>
        <tr>
            <td><?php echo $b['id']; ?></td>
            <td><?php echo htmlspecialchars($b['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($b['beautician_name']); ?></td>
            <td><?php echo htmlspecialchars($b['service_name']); ?></td>
            <td>Rs. <?php echo number_format($b['price'], 0); ?></td>
            <td><?php echo date('d M Y', strtotime($b['booking_date'])); ?></td>
            <td><?php echo date('h:i A', strtotime($b['booking_time'])); ?></td>
            <td>
                <?php if($b['status'] == 'pending'): ?>
                <form method="POST" class="inline-action-form" onsubmit="return confirm('Confirm this booking?')">
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                    <input type="hidden" name="action" value="confirm">
                    <button type="submit" class="btn-confirm">Confirm</button>
                </form>
                <form method="POST" class="inline-action-form" onsubmit="return confirm('Cancel this booking?')">
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="btn-cancel-action">Cancel</button>
                </form>
                <?php else: ?>
                <span class="badge <?php echo htmlspecialchars($b['status']); ?>"><?php echo ucfirst($b['status']); ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php if($total_pages > 1): ?>
    <div class="pagination">
        <?php
        $query_params = [];
        if ($status_filter) $query_params['status'] = $status_filter;
        if ($search) $query_params['search'] = $search;

        function build_booking_page_url($page, $query_params) {
            $params = $query_params;
            $params['page'] = $page;
            return 'bookings.php?' . http_build_query($params);
        }
        ?>
        <a href="<?php echo build_booking_page_url($current_page - 1, $query_params); ?>" class="<?php echo $current_page <= 1 ? 'disabled' : ''; ?>">&laquo; Prev</a>
        <?php for($p = 1; $p <= $total_pages; $p++): ?>
            <a href="<?php echo build_booking_page_url($p, $query_params); ?>" class="<?php echo $p == $current_page ? 'active-page' : ''; ?>"><?php echo $p; ?></a>
        <?php endfor; ?>
        <a href="<?php echo build_booking_page_url($current_page + 1, $query_params); ?>" class="<?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">Next &raquo;</a>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="no-data">
        <p>No bookings found.</p>
    </div>
    <?php endif; ?>
</div>
</body>
</html>