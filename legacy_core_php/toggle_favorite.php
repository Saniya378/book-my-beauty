<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'customer') {
    echo json_encode(['error' => 'Not logged in as customer']);
    exit();
}

include 'config.php';

$customer_id = $_SESSION['user_id'];
$beautician_id = isset($_POST['beautician_id']) ? (int)$_POST['beautician_id'] : 0;

if ($beautician_id <= 0) {
    echo json_encode(['error' => 'Invalid beautician']);
    exit();
}

$existing = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT id FROM favorites WHERE customer_id='$customer_id' AND beautician_id='$beautician_id'"));

if ($existing) {
    mysqli_query($conn, "DELETE FROM favorites WHERE id='{$existing['id']}'");
    echo json_encode(['favorited' => false]);
} else {
    mysqli_query($conn, "INSERT INTO favorites (customer_id, beautician_id) VALUES ('$customer_id', '$beautician_id')");
    echo json_encode(['favorited' => true]);
}
?>