<?php
session_start();
include './config.php';

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];
    $conn->query("UPDATE users SET last_activity = NOW() WHERE id = $id");
}

echo json_encode(["success" => true]);
?>
