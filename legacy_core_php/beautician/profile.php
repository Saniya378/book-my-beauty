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

$profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM beautician_profiles WHERE user_id='$user_id'"));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $pic_path = $profile['profile_pic'] ?? null;

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['profile_pic']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $tmp_path = $_FILES['profile_pic']['tmp_name'];

        // Verify the file is actually an image by reading its real content,
        // not just trusting the filename extension.
        $image_info = @getimagesize($tmp_path);
        $allowed_mime = ['image/jpeg', 'image/png'];

        if (!in_array($ext, $allowed)) {
            $error = "Only JPG and PNG images allowed.";
        } elseif ($image_info === false || !in_array($image_info['mime'], $allowed_mime)) {
            $error = "The uploaded file is not a valid image.";
        } elseif ($_FILES['profile_pic']['size'] > 2 * 1024 * 1024) {
            $error = "Image must be smaller than 2MB.";
        } else {
            $new_filename = 'beautician_' . $user_id . '_' . time() . '.' . $ext;
            $upload_path = '../uploads/' . $new_filename;

            if (!file_exists('../uploads')) {
                mkdir('../uploads', 0777, true);
            }

            if (move_uploaded_file($tmp_path, $upload_path)) {
                $pic_path = 'uploads/' . $new_filename;
            } else {
                $error = "Failed to upload image.";
            }
        }
    }

    if (empty($error)) {
        if ($profile) {
            $sql = "UPDATE beautician_profiles SET bio='$bio', location='$location', phone='$phone', profile_pic='$pic_path' WHERE user_id='$user_id'";
        } else {
            $sql = "INSERT INTO beautician_profiles (user_id, bio, location, phone, profile_pic) VALUES ('$user_id', '$bio', '$location', '$phone', '$pic_path')";
        }

        if (mysqli_query($conn, $sql)) {
            $success = "Profile updated successfully!";
            $profile = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM beautician_profiles WHERE user_id='$user_id'"));
        } else {
            $error = "Something went wrong.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .profile-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-darker));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .profile-avatar {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            overflow: hidden;
            border: 2px solid var(--accent);
        }
        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .profile-header h2 { font-size: 20px; font-weight: 700; margin-bottom: 3px; font-family: 'Playfair Display', serif; }
        .profile-header p { font-size: 13px; opacity: 0.8; }
        textarea { height: 120px; resize: vertical; }
        .file-upload-box {
            border: 2px dashed var(--border);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .file-upload-box:hover { border-color: var(--accent); }
        .file-upload-box input[type="file"] {
            margin: 0;
            border: none;
            padding: 0;
        }
        .current-pic-preview {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 12px;
            border: 3px solid var(--accent);
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

<div class="container" style="max-width: 650px;">
    <div class="profile-header">
        <div class="profile-avatar">
            <?php if(!empty($profile['profile_pic'])): ?>
                <img src="../<?php echo htmlspecialchars($profile['profile_pic']); ?>" alt="Profile">
            <?php else: ?>
                <span></span>
            <?php endif; ?>
        </div>
        <div>
            <h2><?php echo htmlspecialchars($_SESSION['user_name']); ?></h2>
            <p>Manage your public profile</p>
        </div>
    </div>

    <div class="form-box" style="max-width: 100%; padding: 30px;">
        <h3 style="color: var(--primary); font-size: 17px; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #f0e6d3; font-family: 'Playfair Display', serif;">Edit Profile</h3>

        <?php if($success) echo "<p class='success'>$success</p>"; ?>
        <?php if($error) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>

        <form method="POST" enctype="multipart/form-data">

            <?php if(!empty($profile['profile_pic'])): ?>
            <label>Current Photo</label>
            <div style="text-align:center;">
                <img src="../<?php echo htmlspecialchars($profile['profile_pic']); ?>" class="current-pic-preview">
            </div>
            <?php endif; ?>

            <label>Profile Photo</label>
            <div class="file-upload-box">
                <input type="file" name="profile_pic" accept=".jpg,.jpeg,.png">
                <p style="font-size:11px; color:var(--text-gray); margin-top:8px;">JPG or PNG, max 2MB</p>
            </div>

            <label>Bio</label>
            <textarea name="bio" placeholder="Tell customers about your experience, skills, and specialties..."><?php echo htmlspecialchars($profile['bio'] ?? ''); ?></textarea>

            <label>Location</label>
            <input type="text" name="location" placeholder="e.g. Gilgit, GB" value="<?php echo htmlspecialchars($profile['location'] ?? ''); ?>">

            <label>Phone Number</label>
            <input type="text" name="phone" placeholder="e.g. 03001234567" value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">

            <button type="submit">Save Profile</button>
        </form>
    </div>
</div>
<?php include '../chatbot_widget.php'; ?>
</body>
</html>