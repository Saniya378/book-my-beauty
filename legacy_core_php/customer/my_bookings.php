<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$customer_id = $_SESSION['user_id'];

// Handle cancellation with 2 hour window
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel'])) {
    $cancel_id = mysqli_real_escape_string($conn, $_POST['cancel']);

    $booking_check = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT created_at FROM bookings WHERE id='$cancel_id' AND customer_id='$customer_id'"));

    if ($booking_check) {
        $booked_time = strtotime($booking_check['created_at']);
        $hours_passed = (time() - $booked_time) / 3600;

        if ($hours_passed <= 2) {
            // Notify beautician
            $binfo = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT s.service_name, bp.user_id as b_user_id 
                 FROM bookings b 
                 JOIN services s ON b.service_id = s.id
                 JOIN beautician_profiles bp ON s.beautician_id = bp.id
                 WHERE b.id='$cancel_id'"));

            if ($binfo) {
                $cname = $_SESSION['user_name'];
                $cmsg = mysqli_real_escape_string($conn, "$cname cancelled their {$binfo['service_name']} booking");
                mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ('{$binfo['b_user_id']}', '$cmsg')");
            }

            mysqli_query($conn, "UPDATE bookings SET status='cancelled' WHERE id='$cancel_id' AND customer_id='$customer_id'");
        } else {
            $_SESSION['cancel_error'] = "Cancellation window expired. Bookings can only be cancelled within 2 hours.";
        }
    }
    header("Location: my_bookings.php");
    exit();
}

$cancel_error = "";
if (isset($_SESSION['cancel_error'])) {
    $cancel_error = $_SESSION['cancel_error'];
    unset($_SESSION['cancel_error']);
}

$bookings = mysqli_query($conn,
    "SELECT b.id, b.booking_date, b.booking_time, b.status, b.created_at,
            s.service_name, s.price, s.duration,
            u.name as beautician_name
     FROM bookings b
     JOIN services s ON b.service_id = s.id
     JOIN beautician_profiles bp ON s.beautician_id = bp.id
     JOIN users u ON bp.user_id = u.id
     WHERE b.customer_id = '$customer_id'
     ORDER BY b.booking_date DESC");

$total_count = mysqli_num_rows($bookings);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
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
            position: relative;
            overflow: hidden;
        }
        .page-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: var(--accent);
        }
        .page-header h2 { font-size: 20px; font-weight: 700; font-family: 'Playfair Display', serif; }
        .page-header p { font-size: 13px; opacity: 0.8; margin-top: 4px; }
        .total-badge {
            background: rgba(255,255,255,0.15);
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }
        .no-bookings {
            background: white;
            padding: 50px 30px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            box-shadow: var(--shadow);
        }
        .no-bookings a {
            display: inline-block !important;
            margin-top: 15px;
            background: var(--primary);
            color: white;
            padding: 10px 25px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .no-bookings a:hover { background: var(--accent); color: var(--primary); }
        .time-remaining {
            font-size: 10px;
            color: var(--text-gray);
            margin-top: 3px;
        }
        .cancel-form { display: inline; }
        .cancel-btn-inline {
            width: auto !important;
            display: inline-block !important;
            background: none;
            border: none;
            color: #c62828;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            padding: 0 !important;
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
        <div>
            <h2>My Bookings</h2>
            <p>View and track all your beauty appointments</p>
        </div>
        <div class="total-badge"><?php echo $total_count; ?> Bookings</div>
    </div>

    <?php if($cancel_error): ?>
    <div style="background:#fce4ec; color:#c62828; padding:12px 18px; border-radius:8px; margin-bottom:20px; font-size:13px; border-left:4px solid #c62828;">
        <?php echo htmlspecialchars($cancel_error); ?>
    </div>
    <?php endif; ?>

    <?php if($total_count > 0): ?>
    <table>
        <tr>
            <th>#</th>
            <th>Beautician</th>
            <th>Service</th>
            <th>Price</th>
            <th>Date</th>
            <th>Time</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($b = mysqli_fetch_assoc($bookings)): ?>
        <?php
        $booked_time = strtotime($b['created_at']);
        $hours_passed = (time() - $booked_time) / 3600;
        $can_cancel = $hours_passed <= 2;
        ?>
        <tr>
            <td><?php echo $b['id']; ?></td>
            <td><?php echo htmlspecialchars($b['beautician_name']); ?></td>
            <td><?php echo htmlspecialchars($b['service_name']); ?></td>
            <td>Rs. <?php echo number_format($b['price'], 0); ?></td>
            <td><?php echo date('d M Y', strtotime($b['booking_date'])); ?></td>
            <td><?php echo date('h:i A', strtotime($b['booking_time'])); ?></td>
            <td>
                <span class="badge <?php echo htmlspecialchars($b['status']); ?>"><?php echo ucfirst($b['status']); ?></span>
                <a href="download_receipt.php?id=<?php echo $b['id']; ?>" style="display:block; margin-top:4px; font-size:10px; color:var(--text-gray); text-decoration:underline;" target="_blank">Receipt</a>
            </td>
            <td>
                <?php if($b['status'] == 'pending' || $b['status'] == 'confirmed'): ?>
                    <?php if($can_cancel): ?>
                    <div>
                        <form method="POST" class="cancel-form" onsubmit="return confirm('Cancel this booking?')">
                            <input type="hidden" name="cancel" value="<?php echo $b['id']; ?>">
                            <button type="submit" class="cancel-btn-inline">Cancel</button>
                        </form>
<?php
$time_left = max(0, (2 * 3600) - (time() - $booked_time));
$hours = floor($time_left / 3600);
$minutes = floor(($time_left % 3600) / 60);
?>
<div class="time-remaining">
    <?php echo $hours; ?>h <?php echo $minutes; ?>m left
</div>
                    </div>
                    <?php else: ?>
                    <span style="color:#ccc; font-size:13px;">—</span>
                    <?php endif; ?>
                <?php else: ?>
                <span style="color:#ccc; font-size:13px;">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <div class="no-bookings">
        <p>No bookings yet.</p>
        <a href="browse.php">Browse Beauticians</a>
    </div>
    <?php endif; ?>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>