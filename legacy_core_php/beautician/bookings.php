<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'beautician') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$user_id = $_SESSION['user_id'];
$profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM beautician_profiles WHERE user_id='$user_id'"));

if (!$profile) {
    header("Location: profile.php");
    exit();
}

$profile_id = $profile['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && isset($_POST['id'])) {
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    $bid = mysqli_real_escape_string($conn, $_POST['id']);
    if (in_array($action, ['confirmed', 'cancelled'])) {
        // Only allow updating bookings that belong to this beautician's own services
        mysqli_query($conn, "UPDATE bookings b
            JOIN services s ON b.service_id = s.id
            SET b.status='$action'
            WHERE b.id='$bid' AND s.beautician_id='$profile_id'");
    }
    header("Location: bookings.php");
    exit();
}

$bookings = mysqli_query($conn,
    "SELECT b.id, b.booking_date, b.booking_time, b.status,
            s.service_name, s.price,
            u.name as customer_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN users u ON b.customer_id = u.id
     WHERE s.beautician_id = '$profile_id'
     ORDER BY b.booking_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Appointments</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
        }
        .page-header h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; }
        .page-header p { font-size: 13px; opacity: 0.85; }
        .inline-action-form { display: inline; }
        .btn {
            width: auto !important;
            display: inline-block !important;
            width: auto !important;
            display: inline-block !important;
            padding: 5px 12px !important;
            border-radius: 15px;
            border: none;
            font-size: 12px;
            font-weight: 600;
            margin-right: 5px;
            margin-bottom: 0;
            transition: all 0.2s;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
        }
        .btn-confirm { background: #e8f5e9; color: #2e7d32; }
        .btn-confirm:hover { background: #2e7d32; color: white; }
        .btn-cancel { background: #fce4ec; color: #c62828; }
        .btn-cancel:hover { background: #c62828; color: white; }
        .no-bookings {
            background: white;
            padding: 50px 30px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            box-shadow: var(--shadow);
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
<div class="container" style="max-width: 950px;">
    <div class="page-header">
        <h2>Customer Appointments</h2>
        <p>Manage and respond to your booking requests</p>
    </div>

    <?php if(mysqli_num_rows($bookings) > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Customer</th>
            <th>Service</th>
            <th>Price</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($b = mysqli_fetch_assoc($bookings)): ?>
        <tr>
            <td><?php echo $b['id']; ?></td>
            <td><?php echo htmlspecialchars($b['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($b['service_name']); ?></td>
            <td>Rs. <?php echo number_format($b['price'], 0); ?></td>
            <td><?php echo date('d M Y', strtotime($b['booking_date'])); ?></td>
            <td><?php echo date('h:i A', strtotime($b['booking_time'])); ?></td>
            <td><span class="badge <?php echo htmlspecialchars($b['status']); ?>"><?php echo ucfirst($b['status']); ?></span></td>
            <td>
                <?php if($b['status'] == 'pending'): ?>
                <form method="POST" class="inline-action-form">
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                    <input type="hidden" name="action" value="confirmed">
                    <button type="submit" class="btn btn-confirm">Confirm</button>
                </form>
                <form method="POST" class="inline-action-form">
                    <input type="hidden" name="id" value="<?php echo $b['id']; ?>">
                    <input type="hidden" name="action" value="cancelled">
                    <button type="submit" class="btn btn-cancel">Cancel</button>
                </form>
                <?php else: ?>
                <span style="color: var(--text-gray); font-size: 13px;">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <div class="no-bookings">
        <p>No appointments yet. Share your profile to get bookings!</p>
    </div>
    <?php endif; ?>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>