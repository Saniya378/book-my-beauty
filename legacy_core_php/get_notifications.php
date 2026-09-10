<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['count' => 0, 'notifications' => []]);
    exit();
}

include 'config.php';
$user_id = mysqli_real_escape_string($conn, $_SESSION['user_id']);

if (isset($_GET['mark_read'])) {
    mysqli_query($conn, "UPDATE notifications SET is_read=1 WHERE user_id='$user_id'");
    echo json_encode(['success' => true]);
    exit();
}

$result = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id='$user_id' ORDER BY created_at DESC LIMIT 10");
$notifications = [];
$unread = 0;

while($n = mysqli_fetch_assoc($result)) {
    $n['message'] = htmlspecialchars($n['message']);
    $notifications[] = $n;
    if ($n['is_read'] == 0) $unread++;
}

echo json_encode(['count' => $unread, 'notifications' => $notifications]);
?>