<?php
session_start();
$timeout_duration = 10; 

if (!isset($_SESSION['user_id'])) {
    // header("Location: /connectify-web/login.php");
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(401);
        echo json_encode(["error" => "Session expired"]);
    } else {
        header("Location: /connectify-web/login.php");
    }
    exit();
    exit();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    // header("Location: /connectify-web/login.php?message=Session expired, please login again.");
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        http_response_code(401);
        echo json_encode(["error" => "Session expired"]);
    } else {
        header("Location: /connectify-web/login.php?message=Session expired, please login again");
    }
    exit();
}
$_SESSION['last_activity'] = time();
?>
