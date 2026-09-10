<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    header("Location: ../login.php");
    exit();
}
include '../config.php';

$customer_id = $_SESSION['user_id'];

$favorites = mysqli_query($conn,
    "SELECT u.name, bp.id, bp.bio, bp.location, bp.phone, bp.profile_image, bp.profile_pic,
        (SELECT AVG(rating) FROM reviews WHERE beautician_id = bp.id) as avg_rating,
        (SELECT COUNT(*) FROM reviews WHERE beautician_id = bp.id) as review_count
     FROM favorites f
     JOIN beautician_profiles bp ON f.beautician_id = bp.id
     JOIN users u ON bp.user_id = u.id
     WHERE f.customer_id = '$customer_id'
     ORDER BY f.created_at DESC");
$total = mysqli_num_rows($favorites);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites</title>
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
            position: relative;
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
        .beautician-card img {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--accent);
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
        }
        .fav-btn svg { width: 16px; height: 16px; }
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
        .no-result {
            color: var(--text-gray);
            font-size: 14px;
            padding: 50px;
            background: white;
            border-radius: var(--border-radius);
            text-align: center;
            width: 100%;
        }
        .no-result a {
            display: inline-block;
            margin-top: 15px;
            background: var(--primary);
            color: white;
            padding: 10px 25px;
            border-radius: 3px;
            text-decoration: none;
            font-size: 12px;
            font-weight: 700;
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
        <h2>My Favorites</h2>
        <p>Beauticians you've saved for later</p>
    </div>

    <div class="cards">
        <?php if($total > 0): ?>
            <?php while($b = mysqli_fetch_assoc($favorites)): ?>
            <?php $photo = !empty($b['profile_pic']) ? '../' . $b['profile_pic'] : ($b['profile_image'] ?? 'https://i.pravatar.cc/150?img=47'); ?>
            <div class="beautician-card" id="card-<?php echo $b['id']; ?>">
                <div class="card-photo-area">
                    <div class="fav-btn" onclick="removeFavorite(<?php echo $b['id']; ?>)" title="Remove from favorites">
                        <svg viewBox="0 0 24 24" fill="#c62828" stroke="#c62828" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                    </div>
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
                    <a href="view_beautician.php?id=<?php echo $b['id']; ?>" class="btn-book">View Profile</a>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-result">
                <p>No favorites yet. Browse beauticians and tap the heart icon to save your favorites here.</p>
                <a href="browse.php">Browse Beauticians</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function removeFavorite(beauticianId) {
    fetch('../toggle_favorite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'beautician_id=' + beauticianId
    })
    .then(r => r.json())
    .then(data => {
        if (data.favorited === false) {
            const card = document.getElementById('card-' + beauticianId);
            if (card) card.remove();
        }
    });
}
</script>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>