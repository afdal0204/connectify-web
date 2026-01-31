<?php
include './config.php';
include 'maintenance.php';
session_start();

$error = $_SESSION['error'] ?? null;
unset($_SESSION['error']);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $work_id  = trim($_POST['work_id'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? '/connectify-web/pages/dashboard.php';

    if (!$work_id || !$password) {
        $_SESSION['error'] = "Work ID and password are required!";
        header("Location: login.php?redirect=" . urlencode($redirect));
        exit();
    }

    $stmt = $conn->prepare("SELECT u.id, u.work_id, u.name, u.password, 
                                u.department_id, d.department_name, u.role_id, ur.role_name 
                                FROM users u
                                LEFT JOIN department d ON u.department_id = d.id
                                LEFT JOIN user_role ur ON u.role_id = ur.id
                                WHERE u.work_id = ?");
    if (!$stmt) {
        $_SESSION['error'] = "Database error: " . $conn->error;
        header("Location: login.php?redirect=" . urlencode($redirect));
        exit();
    }

    $stmt->bind_param("s", $work_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            session_regenerate_id(true);

            $conn->query("UPDATE users SET is_online = 1, last_activity = NOW() WHERE id = {$user['id']}");

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['work_id'] = $user['work_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['department_id'] = $user['department_id'];
            $_SESSION['department_name'] = $user['department_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['role_id'] = $user['role_id'];
            $_SESSION['role_name'] = $user['role_name'];

            $_SESSION['last_activity'] = time();

            header("Location: " . $redirect);
            exit();
        } else {
            $_SESSION['error'] = "Wrong Password!";
            header("Location: login.php?redirect=" . urlencode($redirect));
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found!";
        header("Location: login.php?redirect=" . urlencode($redirect));
        exit();
    }
}

if (isset($_SESSION['user_id'])) {
    header("Location: /connectify-web/pages/dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="keyword" content="">
    <meta name="author" content="theme_ocean">
    <title>Connectiy</title>
    <link rel="shortcut icon" type="image/x-icon" href="assets/images/favicon.ico">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/theme.min.css">
</head>

<body>
    <main class="auth-minimal-wrapper">
        <div class="auth-minimal-inner">
            <div class="minimal-card-wrapper">
                <div class="card mb-4 mt-5 mx-4 mx-sm-0 position-relative">
                    <div class="wd-80 bg-white p-2 rounded-circle shadow-lg position-absolute translate-middle top-0 start-50">
                        <img src="assets/images/logo.png" alt="" class="img-fluid">
                    </div>
                    <div class="card-body p-sm-5">
                        <h2 class="fs-20 fw-bolder mb-6">Login</h2>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlentities($error) ?></div>
                        <?php endif; ?>
                        <?php if (isset($_GET['message'])): ?>
                            <div class="alert alert-warning">
                                <?= htmlspecialchars($_GET['message']) ?>
                            </div>
                        <?php endif; ?>
                        <form method="POST" action="" class="w-100 mt-4 pt-2">
                            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '/connectify-web/pages/dashboard.php') ?>">
                            <div class="mb-4">
                                <input id="inputWorkId" type="text" name="work_id" class="form-control" placeholder="Work ID" required>
                            </div>
                            <div class="mb-3">
                                <input id="inputPassword" type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>

                                </div>
                                <div>
                                    <a href="#" id="forgetPassword" class="fs-11 text-primary">Forget password?</a>
                                </div>
                            </div>
                            <div class="mt-4">
                                <button type="submit" class="btn btn-lg btn-primary w-100">Login</button>
                            </div>
                        </form>

                        <div class="mt-5 text-muted">
                            <span> Don't have an account?</span>
                            <a href="#" class="fw-bold">Please contact your admin</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Theme Setting -->
    <div class="theme-customizer">
        <div class="customizer-handle">
            <a href="javascript:void(0);" class="cutomizer-open-trigger bg-primary">
                <i class="feather-settings"></i>
            </a>
        </div>
        <div class="customizer-sidebar-wrapper">
            <div class="customizer-sidebar-header px-4 ht-80 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Theme Settings</h5>
                <a href="javascript:void(0);" class="cutomizer-close-trigger d-flex">
                    <i class="feather-x"></i>
                </a>
            </div>
            <div class="customizer-sidebar-body position-relative p-4" data-scrollbar-target="#psScrollbarInit">
                <div class="position-relative px-3 pb-3 pt-4 mt-3 mb-5 border border-gray-2 theme-options-set">
                    <label class="py-1 px-2 fs-8 fw-bold text-uppercase text-muted text-spacing-2 bg-white border border-gray-2 position-absolute rounded-2 options-label" style="top: -12px">Skins</label>
                    <div class="row g-2 theme-options-items app-skin" id="appSkinList">
                        <div class="col-6 text-center position-relative single-option light-button active">
                            <input type="radio" class="btn-check" id="app-skin-light" name="app-skin" value="1" data-app-skin="app-skin-light">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-light">Light</label>
                        </div>
                        <div class="col-6 text-center position-relative single-option dark-button">
                            <input type="radio" class="btn-check" id="app-skin-dark" name="app-skin" value="2" data-app-skin="app-skin-dark">
                            <label class="py-2 fs-9 fw-bold text-dark text-uppercase text-spacing-1 border border-gray-2 w-100 h-100 c-pointer position-relative options-label" for="app-skin-dark">Dark</label>
                        </div>
                    </div>
                </div>
                <div class="customizer-sidebar-footer px-4 ht-60 border-top d-flex align-items-center gap-2">
                    <div class="flex-fill w-50">
                        <a href="javascript:void(0);" class="btn btn-danger" data-style="reset-all-common-style">Reset</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="assets/vendors/js/vendors.min.js"></script>
    <script src="assets/js/common-init.min.js"></script>
    <script src="assets/js/theme-customizer-init.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const link = document.getElementById("forgetPassword");
            link.addEventListener("click", function(e) {
                e.preventDefault();
                alert("Please contact your admin.");
            })
        });
    </script>
</body>

</html>