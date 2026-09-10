<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$customer_id = $_SESSION['user_id'];
$beautician_id = isset($_GET['beautician_id']) ? (int)$_GET['beautician_id'] : 0;
$success = "";
$error = "";

$beautician = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT u.name, bp.id, bp.profile_image
     FROM beautician_profiles bp
     JOIN users u ON bp.user_id = u.id
     WHERE bp.id='$beautician_id'"));

if (!$beautician) {
    header("Location: browse.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    $existing = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT id FROM reviews WHERE customer_id='$customer_id' AND beautician_id='$beautician_id'"));

    if ($existing) {
        $error = "You have already reviewed this beautician.";
    } elseif ($rating < 1 || $rating > 5) {
        $error = "Please select a rating.";
    } else {
        $sql = "INSERT INTO reviews (customer_id, beautician_id, rating, comment)
                VALUES ('$customer_id', '$beautician_id', '$rating', '$comment')";
        if (mysqli_query($conn, $sql)) {
            $success = "Review submitted successfully! ✅";
        } else {
            $error = "Something went wrong.";
        }
    }
}

$reviews = mysqli_query($conn,
    "SELECT r.rating, r.comment, r.created_at, u.name as customer_name
     FROM reviews r
     JOIN users u ON r.customer_id = u.id
     WHERE r.beautician_id='$beautician_id'
     ORDER BY r.created_at DESC");

$avg = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT AVG(rating) as avg_rating, COUNT(*) as total
     FROM reviews WHERE beautician_id='$beautician_id'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews</title>
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
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #f3e5f5;
        }
        .beautician-header h2 {
            color: var(--primary);
            font-size: 20px;
            margin-bottom: 5px;
        }
        .rating-summary {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .rating-big {
            font-size: 36px;
            font-weight: 700;
            color: var(--primary);
        }
        .stars { color: #f4c430; font-size: 18px; }
        .rating-count { color: var(--text-gray); font-size: 13px; }
        .review-card {
            background: white;
            padding: 20px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 15px;
            border-left: 4px solid var(--primary);
        }
        .review-card .reviewer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        .review-card .name {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
        }
        .review-card .date {
            color: var(--text-gray);
            font-size: 12px;
        }
        .review-card .comment {
            color: var(--text-gray);
            font-size: 13px;
            line-height: 1.6;
            margin-top: 8px;
        }
        .section-title {
            color: var(--primary);
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 15px;
            margin-top: 30px;
        }
        .no-reviews {
            background: white;
            padding: 30px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            font-size: 14px;
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
        <img src="<?php echo htmlspecialchars($beautician['profile_image'] ?? 'https://i.pravatar.cc/150?img=47'); ?>" alt="">
        <div>
            <h2><?php echo htmlspecialchars($beautician['name']); ?></h2>
            <div class="rating-summary">
                <?php if($avg['total'] > 0): ?>
                <span class="rating-big"><?php echo number_format($avg['avg_rating'], 1); ?></span>
                <div>
                    <div class="stars"><?php echo str_repeat('⭐', round($avg['avg_rating'])); ?></div>
                    <div class="rating-count"><?php echo $avg['total']; ?> reviews</div>
                </div>
                <?php else: ?>
                <span class="rating-count">No reviews yet</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-box" style="max-width: 100%; padding: 30px; margin-bottom: 25px;">
        <h3 style="color: var(--primary); font-size: 17px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f3e5f5;">✍️ Write a Review</h3>
        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <label>Rating</label>
            <select name="rating" required>
                <option value="">-- Select Rating --</option>
                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                <option value="4">⭐⭐⭐⭐ Good</option>
                <option value="3">⭐⭐⭐ Average</option>
                <option value="2">⭐⭐ Poor</option>
                <option value="1">⭐ Very Poor</option>
            </select>
            <label>Comment</label>
            <textarea name="comment" placeholder="Share your experience..." style="height: 100px; resize: vertical;"></textarea>
            <button type="submit">Submit Review</button>
        </form>
    </div>

    <p class="section-title">Customer Reviews</p>
    <?php if(mysqli_num_rows($reviews) > 0): ?>
        <?php while($r = mysqli_fetch_assoc($reviews)): ?>
        <div class="review-card">
            <div class="reviewer">
                <span class="name"><?php echo htmlspecialchars($r['customer_name']); ?></span>
                <span class="date"><?php echo date('d M Y', strtotime($r['created_at'])); ?></span>
            </div>
            <div class="stars"><?php echo str_repeat('⭐', $r['rating']); ?></div>
            <div class="comment"><?php echo htmlspecialchars($r['comment']); ?></div>
        </div>
        <?php endwhile; ?>
    <?php else: ?>
    <div class="no-reviews">No reviews yet. Be the first to review!</div>
    <?php endif; ?>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>