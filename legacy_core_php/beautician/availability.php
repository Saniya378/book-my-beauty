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
$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_availability'])) {
    $days = isset($_POST['days']) ? $_POST['days'] : [];
    $start = mysqli_real_escape_string($conn, $_POST['start_time']);
    $end = mysqli_real_escape_string($conn, $_POST['end_time']);

    if (empty($days)) {
        $error = "Please select at least one day.";
    } else {
        $added = 0;
        $skipped = [];

        foreach ($days as $day) {
            $day = mysqli_real_escape_string($conn, $day);
            $existing = mysqli_fetch_assoc(mysqli_query($conn,
                "SELECT id FROM availability WHERE beautician_id='$profile_id' AND day_of_week='$day'"));

            if ($existing) {
                $skipped[] = $day;
            } else {
                mysqli_query($conn, "INSERT INTO availability (beautician_id, day_of_week, start_time, end_time)
                    VALUES ('$profile_id', '$day', '$start', '$end')");
                $added++;
            }
        }

        if ($added > 0) {
            $success = "$added day(s) added successfully!";
        }
        if (!empty($skipped)) {
            $error = "Already set for: " . implode(', ', $skipped) . ". Delete first to update.";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_slot'])) {
    $del_id = mysqli_real_escape_string($conn, $_POST['delete_slot']);
    mysqli_query($conn, "DELETE FROM availability WHERE id='$del_id' AND beautician_id='$profile_id'");
    header("Location: availability.php");
    exit();
}

$slots = mysqli_query($conn, "SELECT * FROM availability WHERE beautician_id='$profile_id' ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Availability</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .page-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
        }
        .page-header h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; font-family: 'Playfair Display', serif; }
        .page-header p { font-size: 13px; opacity: 0.8; }
        .form-box { max-width: 100%; padding: 30px; margin-bottom: 25px; }
        .form-box h3 {
            color: var(--primary);
            font-size: 17px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0e6d3;
            font-family: 'Playfair Display', serif;
        }
        .days-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 20px;
        }
        .day-checkbox {
            border: 2px solid var(--border);
            border-radius: 8px;
            padding: 12px 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .day-checkbox input { display: none; }
        .day-checkbox.checked {
            border-color: var(--accent);
            background: var(--accent-light);
            color: var(--primary);
        }
        .time-grid { display: flex; gap: 15px; }
        .time-grid > div { flex: 1; }
        .section-title {
            color: var(--primary);
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }
        .day-badge {
            display: inline-block !important;
            background: var(--accent-light);
            color: var(--primary);
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
        }
        .no-slots {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            font-size: 14px;
            box-shadow: var(--shadow);
        }
        .delete-form { display: inline; }
        .delete-btn-inline {
            width: auto !important;
            display: inline-block !important;
            background: none;
            border: none;
            color: #c62828;
            text-decoration: none;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            padding: 0 !important;
        }
        .delete-btn-inline:hover { text-decoration: underline; }

        @media (max-width: 600px) {
            .days-grid { grid-template-columns: repeat(2, 1fr); }
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
<div class="container" style="max-width: 700px;">
    <div class="page-header">
        <h2>My Availability</h2>
        <p>Set the days and times when you are available for bookings</p>
    </div>

    <div class="form-box card">
        <h3>Add Available Days</h3>
        <?php if($success) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <form method="POST">
            <input type="hidden" name="save_availability" value="1">
            <label>Select Days (you can choose multiple)</label>
            <div class="days-grid">
                <?php
                $week_days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                foreach($week_days as $d):
                ?>
                <label class="day-checkbox" id="label-<?php echo $d; ?>">
                    <input type="checkbox" name="days[]" value="<?php echo $d; ?>" onchange="toggleDay('<?php echo $d; ?>')">
                    <?php echo substr($d, 0, 3); ?>
                </label>
                <?php endforeach; ?>
            </div>

            <div class="time-grid">
                <div>
                    <label>Start Time</label>
                    <input type="time" name="start_time" required>
                </div>
                <div>
                    <label>End Time</label>
                    <input type="time" name="end_time" required>
                </div>
            </div>

            <button type="submit">Add Selected Days</button>
        </form>
    </div>

    <p class="section-title">My Weekly Schedule</p>

    <?php if(mysqli_num_rows($slots) > 0): ?>
    <table>
        <tr>
            <th>Day</th>
            <th>Start Time</th>
            <th>End Time</th>
            <th>Action</th>
        </tr>
        <?php while($s = mysqli_fetch_assoc($slots)): ?>
        <tr>
            <td><span class="day-badge"><?php echo htmlspecialchars($s['day_of_week']); ?></span></td>
            <td><?php echo date('h:i A', strtotime($s['start_time'])); ?></td>
            <td><?php echo date('h:i A', strtotime($s['end_time'])); ?></td>
            <td>
                <form method="POST" class="delete-form" onsubmit="return confirm('Delete this slot?')">
                    <input type="hidden" name="delete_slot" value="<?php echo $s['id']; ?>">
                    <button type="submit" class="delete-btn-inline">Delete</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <?php else: ?>
    <div class="no-slots">
        <p>No availability set yet. Add your working days above.</p>
    </div>
    <?php endif; ?>
</div>

<script>
function toggleDay(day) {
    const label = document.getElementById('label-' + day);
    const checkbox = label.querySelector('input');
    label.classList.toggle('checked', checkbox.checked);
}
</script>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>