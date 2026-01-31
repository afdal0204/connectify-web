<?php
session_start();
include './config.php';

if (isset($_SESSION['user_id'])) {
    $id = $_SESSION['user_id'];

    $stmt = $conn->query("UPDATE users SET is_online = 0 WHERE id = $id");
}

session_unset();
session_destroy();
header("Location: /connectify-web/login.php");
exit();
?>
