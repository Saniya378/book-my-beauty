<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'beautician') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$user_id = $_SESSION['user_id'];
$success = "";
$error = "";

$profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM beautician_profiles WHERE user_id='$user_id'"));

if (!$profile) {
    header("Location: profile.php");
    exit();
}

$profile_id = $profile['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_service'])) {
    $service_name = mysqli_real_escape_string($conn, $_POST['service_name']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    $sql = "INSERT INTO services (beautician_id, service_name, price, duration, description)
            VALUES ('$profile_id', '$service_name', '$price', '$duration', '$description')";

    if (mysqli_query($conn, $sql)) {
        $success = "Service added successfully!";
    } else {
        $error = "Something went wrong.";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['toggle_service']) && isset($_POST['new_status'])) {
    $toggle_id = mysqli_real_escape_string($conn, $_POST['toggle_service']);
    $new_status = (int)$_POST['new_status'] === 1 ? 1 : 0;
    mysqli_query($conn, "UPDATE services SET is_active=$new_status WHERE id='$toggle_id' AND beautician_id='$profile_id'");
    header("Location: services.php");
    exit();
}

$services = mysqli_query($conn, "SELECT * FROM services WHERE beautician_id='$profile_id'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Services</title>
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
        .form-box { max-width: 100%; padding: 30px; margin-bottom: 25px; }
        .form-box h3 {
            color: var(--primary);
            font-size: 17px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f3e5f5;
        }
        textarea { height: 80px; resize: vertical; }
        .section-title {
            color: var(--primary);
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        .no-services {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            font-size: 14px;
            box-shadow: var(--shadow);
        }
        .status-form { display: inline; }
        .status-btn {
            width: auto !important;
            display: inline-block !important;
            text-transform: none !important;
            border: none;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            padding: 4px 10px !important;
            border-radius: 3px;
            margin-bottom: 0;
        }
        .status-btn.deactivate { background: #fce4ec; color: #c62828; }
        .status-btn.deactivate:hover { background: #c62828; color: white; }
        .status-btn.activate { background: #e8f5e9; color: #2e7d32; }
        .status-btn.activate:hover { background: #2e7d32; color: white; }
        .active-badge {
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: #e8f5e9;
            color: #2e7d32;
            margin-right: 8px;
        }
        .inactive-badge {
            padding: 4px 12px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: #f5f5f5;
            color: #999;
            margin-right: 8px;
        }
        tr.inactive-row td { opacity: 0.55; }
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

<div class="container" style="max-width: 780px;">
    <div class="page-header">
        <h2>My Services</h2>
        <p>Add and manage the services you offer to customers</p>
    </div>

    <div class="form-box card">
        <h3>Add New Service</h3>
        <?php if($success) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="POST">
            <label>Service Name</label>
            <input type="text" name="service_name" placeholder="e.g. Facial, Hair Color, Bridal Makeup" required>

            <label>Price (Rs.)</label>
            <input type="number" name="price" placeholder="e.g. 1500" required>

            <label>Duration (minutes)</label>
            <input type="number" name="duration" placeholder="e.g. 60" required>

            <label>Description</label>
            <textarea name="description" placeholder="Brief description of the service..."></textarea>

            <button type="submit" name="add_service">Add Service</button>
        </form>
    </div>

    <p class="section-title">My Service Listings</p>

    <?php if(mysqli_num_rows($services) > 0): ?>
    <table>
        <tr>
            <th>Service</th>
            <th>Price</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php while($s = mysqli_fetch_assoc($services)): ?>
        <?php $is_active = !isset($s['is_active']) || $s['is_active'] == 1; ?>
        <tr class="<?php echo $is_active ? '' : 'inactive-row'; ?>">
            <td><?php echo htmlspecialchars($s['service_name']); ?></td>
            <td>Rs. <?php echo number_format($s['price'], 0); ?></td>
            <td><?php echo htmlspecialchars($s['duration']); ?> min</td>
            <td>
                <?php if($is_active): ?>
                <span class="active-badge">Active</span>
                <?php else: ?>
                <span class="inactive-badge">Hidden</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if($is_active): ?>
                <form method="POST" class="status-form" onsubmit="return confirm('Deactivate this service? Customers will no longer see or be able to book it.')">
                    <input type="hidden" name="toggle_service" value="<?php echo $s['id']; ?>">
                    <input type="hidden" name="new_status" value="0">
                    <button type="submit" class="status-btn deactivate">Deactivate</button>
                </form>
                <?php else: ?>
                <form method="POST" class="status-form">
                    <input type="hidden" name="toggle_service" value="<?php echo $s['id']; ?>">
                    <input type="hidden" name="new_status" value="1">
                    <button type="submit" class="status-btn activate">Activate</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <div class="no-services">
        <p>No services added yet. Add your first service above.</p>
    </div>
    <?php endif; ?>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>