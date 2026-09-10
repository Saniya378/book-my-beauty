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

// Upload new portfolio image
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['upload_image'])) {
    $caption = mysqli_real_escape_string($conn, trim($_POST['caption']));

    if (!isset($_FILES['portfolio_image']) || $_FILES['portfolio_image']['error'] != 0) {
        $error = "Please choose an image to upload.";
    } else {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['portfolio_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $tmp_path = $_FILES['portfolio_image']['tmp_name'];

        $image_info = @getimagesize($tmp_path);
        $allowed_mime = ['image/jpeg', 'image/png'];

        // Count existing images to cap portfolio size
        $count_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM portfolio_images WHERE beautician_id='$profile_id'"));
        $existing_count = $count_result['total'];

        if (!in_array($ext, $allowed)) {
            $error = "Only JPG and PNG images allowed.";
        } elseif ($image_info === false || !in_array($image_info['mime'], $allowed_mime)) {
            $error = "The uploaded file is not a valid image.";
        } elseif ($_FILES['portfolio_image']['size'] > 3 * 1024 * 1024) {
            $error = "Image must be smaller than 3MB.";
        } elseif ($existing_count >= 12) {
            $error = "You've reached the maximum of 12 portfolio images. Delete some to add more.";
        } else {
            $new_filename = 'portfolio_' . $profile_id . '_' . time() . '_' . mt_rand(1000, 9999) . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;

            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }

            if (move_uploaded_file($tmp_path, $upload_path)) {
                $db_path = 'uploads/' . $new_filename;
                mysqli_query($conn, "INSERT INTO portfolio_images (beautician_id, image_path, caption) VALUES ('$profile_id', '$db_path', '$caption')");
                $success = "Image added to your portfolio!";
            } else {
                $error = "Failed to upload image.";
            }
        }
    }
}

// Delete portfolio image
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_image'])) {
    $del_id = mysqli_real_escape_string($conn, $_POST['delete_image']);
    $img = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image_path FROM portfolio_images WHERE id='$del_id' AND beautician_id='$profile_id'"));
    if ($img) {
        $file_path = '../' . $img['image_path'];
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
        mysqli_query($conn, "DELETE FROM portfolio_images WHERE id='$del_id' AND beautician_id='$profile_id'");
    }
    header("Location: portfolio.php");
    exit();
}

$images = mysqli_query($conn, "SELECT * FROM portfolio_images WHERE beautician_id='$profile_id' ORDER BY uploaded_at DESC");
$image_count = mysqli_num_rows($images);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Portfolio</title>
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
        }
        .page-header h2 { font-size: 20px; font-weight: 700; margin-bottom: 4px; font-family: 'Playfair Display', serif; }
        .page-header p { font-size: 13px; opacity: 0.85; }
        .count-badge {
            background: var(--accent);
            color: var(--primary);
            padding: 6px 16px;
            border-radius: 3px;
            font-size: 13px;
            font-weight: 700;
        }
        .form-box { max-width: 100%; padding: 30px; margin-bottom: 25px; }
        .form-box h3 {
            color: var(--primary);
            font-size: 17px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0e6d3;
            font-family: 'Playfair Display', serif;
        }
        .file-upload-box {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 16px;
            transition: all 0.2s;
        }
        .file-upload-box:hover { border-color: var(--accent); }
        .section-title {
            color: var(--primary);
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 15px;
            font-family: 'Playfair Display', serif;
        }
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
        }
        .gallery-item {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            transition: all 0.3s;
        }
        .gallery-item:hover { transform: translateY(-4px); box-shadow: var(--shadow-hover); }
        .gallery-item img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            display: block;
        }
        .gallery-item-body {
            padding: 10px 12px;
        }
        .gallery-item-body .caption {
            font-size: 12px;
            color: var(--text-dark);
            margin-bottom: 8px;
            min-height: 16px;
        }
        .delete-form-inline { display: inline; }
        .gallery-delete-btn {
            width: auto !important;
            display: inline-block !important;
            background: none;
            border: none;
            color: #c62828;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Poppins', sans-serif;
            padding: 0 !important;
        }
        .no-images {
            background: white;
            padding: 40px;
            border-radius: var(--border-radius);
            text-align: center;
            color: var(--text-gray);
            font-size: 14px;
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

<div class="container" style="max-width: 780px;">
    <div class="page-header">
        <div>
            <h2>My Portfolio</h2>
            <p>Show off your best work — customers see this on your profile</p>
        </div>
        <div class="count-badge"><?php echo $image_count; ?>/12</div>
    </div>

    <div class="form-box card">
        <h3>Add Photo</h3>
        <?php if($success) echo "<p class='success'>" . htmlspecialchars($success) . "</p>"; ?>
        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="file-upload-box">
                <input type="file" name="portfolio_image" accept=".jpg,.jpeg,.png" required>
                <p style="font-size:11px; color:var(--text-gray); margin-top:8px;">JPG or PNG, max 3MB</p>
            </div>
            <label>Caption (optional)</label>
            <input type="text" name="caption" placeholder="e.g. Bridal makeup, Balayage hair color...">
            <button type="submit" name="upload_image">Add to Portfolio</button>
        </form>
    </div>

    <p class="section-title">My Work</p>

    <?php if($image_count > 0): ?>
    <div class="gallery-grid">
        <?php while($img = mysqli_fetch_assoc($images)): ?>
        <div class="gallery-item">
            <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" alt="Portfolio image">
            <div class="gallery-item-body">
                <div class="caption"><?php echo $img['caption'] ? htmlspecialchars($img['caption']) : ''; ?></div>
                <form method="POST" class="delete-form-inline" onsubmit="return confirm('Remove this photo?')">
                    <input type="hidden" name="delete_image" value="<?php echo $img['id']; ?>">
                    <button type="submit" class="gallery-delete-btn">Remove</button>
                </form>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="no-images">
        <p>No portfolio photos yet. Add your first one above to start showcasing your work.</p>
    </div>
    <?php endif; ?>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>