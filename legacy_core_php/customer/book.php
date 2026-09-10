<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$customer_id = $_SESSION['user_id'];
$beautician_id = isset($_GET['beautician_id']) ? (int)$_GET['beautician_id'] : 0;
$success = "";
$error = "";

$availability = mysqli_query($conn, "SELECT * FROM availability WHERE beautician_id='$beautician_id' ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')");
$available_days = [];
while($a = mysqli_fetch_assoc($availability)) {
    $available_days[$a['day_of_week']] = $a;
}

$beautician = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT u.name, bp.bio, bp.location, bp.phone, bp.profile_image, bp.profile_pic
     FROM beautician_profiles bp
     JOIN users u ON bp.user_id = u.id
     WHERE bp.id='$beautician_id'"));

if (!$beautician) {
    header("Location: browse.php");
    exit();
}

$services = mysqli_query($conn, "SELECT * FROM services WHERE beautician_id='$beautician_id' AND is_active=1");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_id = mysqli_real_escape_string($conn, $_POST['service_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $booking_time = mysqli_real_escape_string($conn, $_POST['booking_time']);

    $service_owner_check = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT id, service_name, price FROM services WHERE id='$service_id' AND beautician_id='$beautician_id' AND is_active=1"));

    $conflict_check = mysqli_query($conn, "SELECT b.id FROM bookings b
        JOIN services s ON b.service_id = s.id
        WHERE s.beautician_id = '$beautician_id'
        AND b.booking_date = '$booking_date'
        AND b.booking_time = '$booking_time'
        AND b.status != 'cancelled'");

    if (!$service_owner_check) {
        $error = "Invalid service selected.";
    } elseif (mysqli_num_rows($conflict_check) > 0) {
        $error = "This time slot is already booked. Please choose a different time.";
    } else {
        $sql = "INSERT INTO bookings (customer_id, service_id, booking_date, booking_time)
                VALUES ('$customer_id', '$service_id', '$booking_date', '$booking_time')";

        if (mysqli_query($conn, $sql)) {
            $success = "Appointment booked successfully!";

            // Notify beautician (in-app notification)
            $b_user_res = mysqli_query($conn, "SELECT bp.user_id FROM beautician_profiles bp WHERE bp.id='$beautician_id'");
            $b_user_data = mysqli_fetch_assoc($b_user_res);
            $b_user = $b_user_data ? $b_user_data['user_id'] : 0;

            $cname = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'A customer';
            $dfmt = date('d M Y', strtotime($booking_date));
            $tfmt = date('h:i A', strtotime($booking_time));
            $msg = mysqli_real_escape_string($conn, "New booking from $cname for $dfmt");

            if ($b_user > 0) {
                mysqli_query($conn, "INSERT INTO notifications (user_id, message) VALUES ('$b_user', '$msg')");
            }

            // Send email confirmation to the customer
            $customer_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name, email FROM users WHERE id='$customer_id'"));

            if ($customer_info && !empty($customer_info['email'])) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = SMTP_EMAIL;
                    $mail->Password = SMTP_APP_PASSWORD;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom(SMTP_EMAIL, SMTP_FROM_NAME);
                    $mail->addAddress($customer_info['email'], $customer_info['name']);

                    $mail->isHTML(true);
                    $mail->Subject = 'Your Beauty Portal appointment is booked!';
                    $mail->Body =
                        "Hi " . htmlspecialchars($customer_info['name']) . ",<br><br>"
                        . "Your appointment has been booked successfully. Here are the details:<br><br>"
                        . "<b>Beautician:</b> " . htmlspecialchars($beautician['name']) . "<br>"
                        . "<b>Service:</b> " . htmlspecialchars($service_owner_check['service_name']) . "<br>"
                        . "<b>Price:</b> Rs. " . number_format($service_owner_check['price'], 0) . "<br>"
                        . "<b>Date:</b> " . $dfmt . "<br>"
                        . "<b>Time:</b> " . $tfmt . "<br><br>"
                        . "You can cancel this booking within 2 hours of making it from the 'My Bookings' page.<br><br>"
                        . "Thank you for using Beauty Portal!";
                    $mail->AltBody = "Your appointment with " . $beautician['name'] . " for " . $service_owner_check['service_name'] . " on " . $dfmt . " at " . $tfmt . " has been booked.";

                    $mail->send();
                } catch (Exception $e) {
                    // Don't block the booking if the email fails to send — just log it silently.
                    error_log("Booking confirmation email failed: " . $mail->ErrorInfo);
                }
            }
        } else {
            $error = "Something went wrong. Try again.";
        }
    }
}

$photo = !empty($beautician['profile_pic']) ? '../' . $beautician['profile_pic'] : ($beautician['profile_image'] ?? 'https://i.pravatar.cc/150?img=47');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Book Appointment</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .beautician-header {
            background: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 25px;
        }
        .beautician-header img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent-light);
        }
        .beautician-header h2 {
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 5px;
            font-family: 'Playfair Display', serif;
        }
        .beautician-header p {
            color: var(--text-gray);
            font-size: 13px;
            margin-bottom: 3px;
        }
        .form-box h3 {
            color: var(--primary);
            font-size: 17px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0e6d3;
            font-family: 'Playfair Display', serif;
        }
        .no-services {
            color: var(--text-gray);
            font-size: 14px;
            padding: 30px;
            text-align: center;
        }
        .no-availability {
            background: #fff3e0;
            color: #e65100;
            padding: 12px 15px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 15px;
        }
        .email-note {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 10px 15px;
            border-radius: 8px;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 15px;
        }
        .navbar div {
            display: flex;
            align-items: center;
            gap: 15px;
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
<div class="container">
    <a href="browse.php" class="back-link">← Back to Browse</a>

    <div class="beautician-header">
        <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($beautician['name']); ?>">
        <div>
            <h2><?php echo htmlspecialchars($beautician['name']); ?></h2>
            <p>📍 <?php echo htmlspecialchars($beautician['location']); ?></p>
            <p>📞 <?php echo htmlspecialchars($beautician['phone']); ?></p>
            <p><?php echo htmlspecialchars($beautician['bio']); ?></p>
        </div>
    </div>

    <div class="form-box" style="max-width: 100%; padding: 30px;">
        <h3>Book an Appointment</h3>

        <?php if($success): ?>
        <p class="success"><?php echo $success; ?></p>
        <p class="email-note">A confirmation email has been sent to your registered email address.</p>
        <?php endif; ?>
        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <?php if(mysqli_num_rows($services) > 0): ?>
        <form method="POST">
            <label>Select Service</label>
            <select name="service_id" required>
                <option value="">-- Choose a service --</option>
                <?php
                mysqli_data_seek($services, 0);
                while($s = mysqli_fetch_assoc($services)): ?>
                <option value="<?php echo $s['id']; ?>">
                    <?php echo htmlspecialchars($s['service_name']); ?> — Rs. <?php echo $s['price']; ?> (<?php echo $s['duration']; ?> min)
                </option>
                <?php endwhile; ?>
            </select>

            <?php if(count($available_days) > 0): ?>
            <label>Select Available Day</label>
            <select name="booking_day" required onchange="updateDate(this)">
                <option value="">-- Select Day --</option>
                <?php foreach($available_days as $day => $slot): ?>
                <option value="<?php echo $day; ?>">
                    <?php echo $day; ?> (<?php echo substr($slot['start_time'],0,5); ?> - <?php echo substr($slot['end_time'],0,5); ?>)
                </option>
                <?php endforeach; ?>
            </select>

            <label>Date</label>
            <input type="date" name="booking_date" id="booking_date" min="<?php echo date('Y-m-d'); ?>" required>

            <label>Time</label>
            <input type="time" name="booking_time"
                min="<?php echo substr(reset($available_days)['start_time'],0,5); ?>"
                max="<?php echo substr(reset($available_days)['end_time'],0,5); ?>"
                required>
            <?php else: ?>
            <div class="no-availability">This beautician has not set availability yet.</div>
            <label>Date</label>
            <input type="date" name="booking_date" min="<?php echo date('Y-m-d'); ?>" required>
            <label>Time</label>
            <input type="time" name="booking_time" required>
            <?php endif; ?>

            <button type="submit">Confirm Booking</button>
        </form>
        <?php else: ?>
        <p class="no-services">This beautician has not added any services yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
function updateDate(select) {
    const day = select.value;
    const dayIndex = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'].indexOf(day);
    let date = new Date();
    let diff = (dayIndex - date.getDay() + 7) % 7;
    if(diff === 0) diff = 7;
    date.setDate(date.getDate() + diff);
    document.getElementById('booking_date').value = date.toISOString().split('T')[0];
}
</script>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>