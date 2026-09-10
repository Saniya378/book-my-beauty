<?php
session_start();

include '../config.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$location_filter = isset($_GET['location']) ? mysqli_real_escape_string($conn, $_GET['location']) : '';
$service_filter = isset($_GET['service']) ? mysqli_real_escape_string($conn, $_GET['service']) : '';
$min_price = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? (int)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? (int)$_GET['max_price'] : 999999;
$rating_filter = isset($_GET['rating']) ? (int)$_GET['rating'] : 0;

$sql = "SELECT u.name, bp.id, bp.bio, bp.location, bp.phone, bp.profile_image, bp.profile_pic,
        (SELECT AVG(rating) FROM reviews WHERE beautician_id = bp.id) as avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE beautician_id = bp.id) as review_count
        FROM beautician_profiles bp 
        JOIN users u ON bp.user_id = u.id
        WHERE (u.name LIKE '%$search%' OR bp.location LIKE '%$search%')
        AND (bp.location LIKE '%$location_filter%')
        AND EXISTS (
            SELECT 1 FROM services s 
            WHERE s.beautician_id = bp.id
            AND s.is_active = 1
            AND (s.service_name LIKE '%$service_filter%' OR '$service_filter' = '')
            AND s.price BETWEEN $min_price AND $max_price
        )
        HAVING (avg_rating >= $rating_filter OR avg_rating IS NULL OR $rating_filter = 0)";

$beauticians = mysqli_query($conn, $sql);

// Get this customer's current favorites (if logged in as customer) to mark hearts filled/outline
$favorited_ids = [];
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer') {
    $cust_id = $_SESSION['user_id'];
    $fav_result = mysqli_query($conn, "SELECT beautician_id FROM favorites WHERE customer_id='$cust_id'");
    while ($f = mysqli_fetch_assoc($fav_result)) {
        $favorited_ids[] = $f['beautician_id'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Browse Beauticians</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .page-header { margin-bottom: 25px; }
        .page-header h2 {
            font-size: 22px;
            color: var(--primary);
            font-weight: 700;
            font-family: 'Playfair Display', serif;
        }
        .page-header p { color: var(--text-gray); font-size: 13px; margin-top: 4px; }

        .search-box {
            background: white;
            padding: 22px 25px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            display: flex;
            gap: 12px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            align-items: center;
            border-top: 3px solid var(--accent);
        }
        .search-box input, .search-box select {
            flex: 1;
            margin-bottom: 0;
            min-width: 130px;
        }
        .search-box button { width: auto; padding: 13px 28px; }

        .cards { display: flex; flex-wrap: wrap; gap: 25px; }
        .beautician-card {
            background: white;
            padding: 0;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            width: 250px;
            text-align: center;
            transition: all 0.3s;
            overflow: hidden;
        }
        .beautician-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }
        .card-photo-area {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            padding: 30px 20px 20px;
            position: relative;
        }
        .fav-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255,255,255,0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 2;
            transition: transform 0.15s;
        }
        .fav-btn:hover { transform: scale(1.1); }
        .fav-btn svg { width: 16px; height: 16px; }
        .beautician-card img {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
        }
        .card-body { padding: 18px 20px 22px; }
        .beautician-card h3 {
            color: var(--primary);
            font-size: 16px;
            margin-bottom: 6px;
            font-family: 'Playfair Display', serif;
        }
        .rating-display {
            font-size: 12px;
            color: var(--accent);
            font-weight: 700;
            margin-bottom: 10px;
        }
        .rating-display .stars { color: #f4c430; }
        .no-rating {
            font-size: 11px;
            color: var(--text-gray);
            margin-bottom: 10px;
        }
        .beautician-card .meta {
            font-size: 12px;
            color: var(--text-gray);
            margin-bottom: 4px;
            text-align: left;
        }
        .beautician-card .bio {
            font-size: 12px;
            color: var(--text-gray);
            text-align: left;
            margin: 10px 0;
            line-height: 1.6;
        }
        .btn-book {
            display: block;
            margin-top: 14px;
            background: var(--primary);
            color: white;
            padding: 10px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s;
        }
        .btn-book:hover { background: var(--accent); color: var(--primary); }
        .btn-review {
            display: block;
            margin-top: 8px;
            background: transparent;
            color: var(--primary);
            padding: 9px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            border: 1.5px solid var(--border);
            transition: all 0.3s;
        }
        .btn-review:hover { border-color: var(--accent); color: var(--accent); }
        .no-result {
            color: var(--text-gray);
            font-size: 14px;
            padding: 50px;
            background: white;
            border-radius: var(--border-radius);
            text-align: center;
            width: 100%;
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
<div class="container" style="max-width: 1050px;">
    <div class="page-header">
        <h2>Browse Beauticians</h2>
        <p>Find and book the perfect beautician for you</p>
    </div>

    <form method="GET" class="search-box">
        <input type="text" name="search" placeholder="Search by name..." value="<?php echo htmlspecialchars($search); ?>">
        <input type="text" name="location" placeholder="Filter by location..." value="<?php echo htmlspecialchars($location_filter); ?>">
        <select name="service">
            <option value="">All Services</option>
            <option value="Makeup" <?php echo $service_filter=='Makeup'?'selected':''; ?>>Makeup</option>
            <option value="Hair" <?php echo $service_filter=='Hair'?'selected':''; ?>>Hair</option>
            <option value="Facial" <?php echo $service_filter=='Facial'?'selected':''; ?>>Facial</option>
            <option value="Mehndi" <?php echo $service_filter=='Mehndi'?'selected':''; ?>>Mehndi</option>
        </select>
        <input type="number" name="min_price" placeholder="Min Rs." value="<?php echo $min_price ?: ''; ?>">
        <input type="number" name="max_price" placeholder="Max Rs." value="<?php echo ($max_price < 999999) ? $max_price : ''; ?>">
        <select name="rating">
            <option value="0">Any Rating</option>
            <option value="4" <?php echo $rating_filter==4?'selected':''; ?>>4+ Stars</option>
            <option value="3" <?php echo $rating_filter==3?'selected':''; ?>>3+ Stars</option>
        </select>
        <button type="submit">Search</button>
    </form>

    <div class="cards">
        <?php if(mysqli_num_rows($beauticians) > 0): ?>
            <?php while($b = mysqli_fetch_assoc($beauticians)): ?>
            <?php $photo = !empty($b['profile_pic']) ? '../' . $b['profile_pic'] : ($b['profile_image'] ?? 'https://i.pravatar.cc/150?img=47'); ?>
            <div class="beautician-card" id="card-<?php echo $b['id']; ?>">
                <div class="card-photo-area">
                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer'): ?>
                    <?php $is_fav = in_array($b['id'], $favorited_ids); ?>
                    <div class="fav-btn" onclick="toggleFavorite(<?php echo $b['id']; ?>, this)" title="Save to favorites">
                        <svg viewBox="0 0 24 24" fill="<?php echo $is_fav ? '#c62828' : 'none'; ?>" stroke="#c62828" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($photo); ?>" alt="<?php echo htmlspecialchars($b['name']); ?>">
                </div>
                <div class="card-body">
                    <h3><?php echo htmlspecialchars($b['name']); ?></h3>
                    <?php if($b['avg_rating']): ?>
                    <div class="rating-display"><span class="stars">★</span> <?php echo number_format($b['avg_rating'], 1); ?> (<?php echo $b['review_count']; ?>)</div>
                    <?php else: ?>
                    <div class="no-rating">No reviews yet</div>
                    <?php endif; ?>
                    <p class="meta">📍 <?php echo $b['location'] ? htmlspecialchars($b['location']) : 'Location not set'; ?></p>
                    <p class="meta">📞 <?php echo $b['phone'] ? htmlspecialchars($b['phone']) : 'Phone not set'; ?></p>
                    <p class="bio"><?php echo $b['bio'] ? htmlspecialchars(substr($b['bio'], 0, 75)).'...' : 'No bio yet'; ?></p>
<a href="view_beautician.php?id=<?php echo $b['id']; ?>" class="btn-book">View Profile</a>
<?php if(isset($_SESSION['user_id']) && $_SESSION['user_role'] == 'customer'): ?>
<a href="book.php?beautician_id=<?php echo $b['id']; ?>" class="btn-review">Book Now</a>
<?php else: ?>
<a href="../login.php" class="btn-review">Login to Book</a>
<?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-result">No beauticians found. Try a different search.</p>
        <?php endif; ?>
    </div>
</div>
<script>
function toggleFavorite(beauticianId, el) {
    fetch('../toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'beautician_id=' + beauticianId
    })
    .then(r => r.json())
    .then(data => {
        const svg = el.querySelector('svg');
        if (data.favorited) {
            svg.setAttribute('fill', '#c62828');
        } else {
            svg.setAttribute('fill', 'none');
        }
    });
}
</script>
<?php include '../chatbot_widget.php'; ?>

</body>
</html>