<?php
session_start();

// set login time saat login pertama
if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
}

// timeout dalam detik (1 jam)
$timeout = 60;

if (time() - $_SESSION['login_time'] > $timeout) {
    session_unset();
    session_destroy();
    
    // jika request via AJAX, kirim JSON
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        echo json_encode([
            "success" => false,
            "message" => "Session expired",
            "redirect" => "/connectify-web/login.php"
        ]);
        exit();
    }

    // redirect normal
    header("Location: /connectify-web/login.php");
    exit();
}
?>
