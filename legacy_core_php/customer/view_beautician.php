<?php
session_start();
include '../config.php';

$beautician_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$beautician = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT u.name, bp.id, bp.bio, bp.location, bp.phone, bp.profile_image, bp.profile_pic
     FROM beautician_profiles bp
     JOIN users u ON bp.user_id = u.id
     WHERE bp.id='$beautician_id'"));

if (!$beautician) {
    header("Location: ../index.php");
    exit();
}

$services = mysqli_query($conn, "SELECT * FROM services WHERE beautician_id='$beautician_id' AND is_active=1");

$portfolio_images = mysqli_query($conn, "SELECT * FROM portfolio_images WHERE beautician_id='$beautician_id' ORDER BY uploaded_at DESC");

$reviews = mysqli_query($conn,
    "SELECT r.rating, r.comment, r.created_at, u.name as customer_name
     FROM reviews r
     JOIN users u ON r.customer_id = u.id
     WHERE r.beautician_id='$beautician_id'
     ORDER BY r.created_at DESC");

$avg = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT AVG(rating) as avg_rating, COUNT(*) as total
     FROM reviews WHERE beautician_id='$beautician_id'"));

$photo = !empty($beautician['profile_pic']) ? '../' . $beautician['profile_pic'] : ($beautician['profile_image'] ?? 'https://i.pravatar.cc/150?img=47');

$is_favorited = false;
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer') {
    $cust_id = $_SESSION['user_id'];
    $fav_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id FROM favorites WHERE customer_id='$cust_id' AND beautician_id='$beautician_id'"));
    $is_favorited = $fav_check ? true : false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($beautician['name']); ?> - Beauty Portal</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .profile-hero {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            padding: 40px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 25px;
            color: white;
            position: relative;
        }
        .fav-btn-profile {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.15s;
        }
        .fav-btn-profile:hover { transform: scale(1.1); }
        .fav-btn-profile svg { width: 20px; height: 20px; }
        .profile-hero img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
        }
        .profile-hero h2 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin-bottom: 6px;
        }
        .profile-hero p { font-size: 13px; opacity: 0.85; margin-bottom: 4px; }
        .rating-big { color: var(--accent); font-size: 16px; font-weight: 700; margin-top: 8px; }
        .section-title {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--accent-light);
        }
        .services-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 35px;
        }
        .service-card {
            background: white;
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            flex: 1;
            min-width: 200px;
            border-left: 4px solid var(--accent);
        }
        .service-card h4 {
            color: var(--primary);
            font-size: 15px;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
        }
        .service-card .price {
            color: var(--accent);
            font-weight: 700;
            font-size: 16px;
        }
        .service-card .duration {
            color: var(--text-gray);
            font-size: 12px;
            margin-top: 4px;
        }
        .book-cta {
            background: var(--primary);
            color: white;
            padding: 30px;
            border-radius: var(--border-radius);
            text-align: center;
            margin-bottom: 35px;
        }
        .book-cta h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            margin-bottom: 8px;
        }
        .book-cta p { font-size: 13px; opacity: 0.8; margin-bottom: 20px; }
        .book-cta .btn-book {
            display: inline-block;
            background: var(--accent);
            color: var(--primary);
            padding: 12px 35px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-right: 10px;
        }
        .book-cta .btn-login {
            display: inline-block;
            border: 2px solid rgba(255,255,255,0.4);
            color: white;
            padding: 12px 25px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .book-cta .btn-review {
            display: inline-block;
            border: 2px solid var(--accent);
            color: var(--accent);
            background: transparent;
            padding: 12px 25px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 13px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .book-cta .btn-review:hover { background: var(--accent); color: var(--primary); }
        .review-card {
            background: white;
            padding: 20px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            margin-bottom: 15px;
            border-left: 4px solid var(--accent);
        }
        .review-card .reviewer {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .review-card .name { font-weight: 600; color: var(--text-dark); font-size: 14px; }
        .review-card .date { color: var(--text-gray); font-size: 12px; }
        .review-card .stars { color: #f4c430; font-size: 15px; margin-bottom: 6px; }
        .review-card .comment { color: var(--text-gray); font-size: 13px; line-height: 1.6; }
        .no-reviews { color: var(--text-gray); font-size: 14px; text-align: center; padding: 30px; background: white; border-radius: var(--border-radius); }
        .portfolio-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 12px;
            margin-bottom: 35px;
        }
        .portfolio-item {
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s;
            cursor: pointer;
        }
        .portfolio-item:hover { transform: translateY(-4px); box-shadow: var(--shadow-hover); }
        .portfolio-item img { width: 100%; height: 130px; object-fit: cover; display: block; }
        .no-portfolio { color: var(--text-gray); font-size: 13px; padding: 20px; background: white; border-radius: var(--border-radius); text-align: center; margin-bottom: 35px; }
    </style>
</head>
<body>
<div class="navbar">
    <h1>Beauty <span style="color:#c9a96e;">Portal</span></h1>
    <div>
        <?php if(isset($_SESSION['user_id'])): ?>
        <a href="dashboard.php">Dashboard</a>
        <?php include '../notification_bell.php'; ?>
        <a href="../logout.php">Logout</a>
        <?php else: ?>
        <a href="../login.php">Login</a>
        <a href="../register.php">Register</a>
        <?php endif; ?>
    </div>
</div>

<div class="container" style="max-width: 850px;">
    <a href="javascript:history.back()" class="back-link">← Back</a>

    <div class="profile-hero">
        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer'): ?>
        <div class="fav-btn-profile" id="fav-btn-profile" onclick="toggleFavoriteProfile(<?php echo $beautician['id']; ?>)" title="Save to favorites">
            <svg viewBox="0 0 24 24" fill="<?php echo $is_favorited ? '#c62828' : 'none'; ?>" stroke="#c62828" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
        </div>
        <?php endif; ?>
        <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($beautician['name']); ?>">
        <div>
            <h2><?php echo htmlspecialchars($beautician['name']); ?></h2>
            <p>📍 <?php echo htmlspecialchars($beautician['location']); ?></p>
            <p>📞 <?php echo htmlspecialchars($beautician['phone']); ?></p>
            <p><?php echo htmlspecialchars($beautician['bio']); ?></p>
            <?php if($avg['total'] > 0): ?>
            <div class="rating-big">★ <?php echo number_format($avg['avg_rating'], 1); ?> / 5 &nbsp;(<?php echo $avg['total']; ?> reviews)</div>
            <?php endif; ?>
        </div>
    </div>

    <p class="section-title">Services Offered</p>
    <?php if(mysqli_num_rows($services) > 0): ?>
    <div class="services-grid">
        <?php while($s = mysqli_fetch_assoc($services)): ?>
        <div class="service-card">
            <h4><?php echo htmlspecialchars($s['service_name']); ?></h4>
            <div class="price">Rs. <?php echo number_format($s['price'], 0); ?></div>
            <div class="duration"><?php echo (int)$s['duration']; ?> minutes</div>
            <?php if($s['description']): ?>
            <p style="font-size:12px; color:var(--text-gray); margin-top:8px;"><?php echo htmlspecialchars($s['description']); ?></p>
            <?php endif; ?>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <p style="color:var(--text-gray); font-size:14px; margin-bottom:30px;">No services listed yet.</p>
    <?php endif; ?>

    <p class="section-title">Portfolio</p>
    <?php if(mysqli_num_rows($portfolio_images) > 0): ?>
    <div class="portfolio-grid">
        <?php while($p = mysqli_fetch_assoc($portfolio_images)): ?>
        <div class="portfolio-item">
            <img src="../<?php echo htmlspecialchars($p['image_path']); ?>" alt="<?php echo htmlspecialchars($p['caption'] ?: 'Portfolio work'); ?>" title="<?php echo htmlspecialchars($p['caption'] ?: ''); ?>">
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="no-portfolio">No portfolio photos yet.</div>
    <?php endif; ?>

    <div class="book-cta">
        <h3>Ready to Book?</h3>
        <p>Login to book an appointment with <?php echo htmlspecialchars($beautician['name']); ?></p>
        <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer'): ?>
        <a href="book.php?beautician_id=<?php echo $beautician['id']; ?>" class="btn-book">Book Now</a>
        <a href="review.php?beautician_id=<?php echo $beautician['id']; ?>" class="btn-review">Leave a Review</a>
        <?php else: ?>
        <a href="../login.php" class="btn-book">Login to Book</a>
        <a href="../register.php" class="btn-login">Register</a>
        <?php endif; ?>
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
    <div class="no-reviews">No reviews yet.</div>
    <?php endif; ?>
</div>
<script>
function toggleFavoriteProfile(beauticianId) {
    fetch('../toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'beautician_id=' + beauticianId
    })
    .then(r => r.json())
    .then(data => {
        const svg = document.querySelector('#fav-btn-profile svg');
        svg.setAttribute('fill', data.favorited ? '#c62828' : 'none');
    });
}
</script>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>