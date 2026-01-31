<?php
include '../config.php';
// include '../init.php';
session_start();
// include '../auth_check.php';

if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: /new-connectify/login.php?redirect={$redirect}");
    exit();
}

$departments = [];
$deptQuery = "SELECT id, department_name FROM department";
$deptResult = $conn->query($deptQuery);
if ($deptResult->num_rows > 0) {
    while ($row = $deptResult->fetch_assoc()) {
        $departments[] = $row;
    }
}

$role_id = $_SESSION['role_id'] ?? 'Guest'; // trigger access menu
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <title>Connectify | Dashboard</title>
    <link rel="shortcut icon" type="image/x-icon" href="/new-connectify/assets/images/favicon.ico" />
    <link rel="stylesheet" type="text/css" href="/new-connectify/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/new-connectify/assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="/new-connectify/assets/vendors/css/daterangepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="/new-connectify/assets/css/theme.min.css" />
    <link rel="stylesheet" type="text/css" href="/new-connectify/assets/css/footer.css" />
    <link href="/new-connectify/assets/public/vendor/DataTables/datatables.min.css" rel="stylesheet">

    <style>
        #mycardreport {
            cursor: pointer;
        }

        #mycardreport:hover {
            text-decoration: underline;
        }

        body {
            display: flex;
            flex-direction: column;
        }
    </style>
</head>

<body>
     <!-- Sidebar navigation-->
    <nav class="nxl-navigation">
        <div class="navbar-wrapper">
            <div class="m-header">
                <a href="dashboard.php" class="b-brand">
                    <img src="/new-connectify/assets/images/logo2.png" alt="" class="logo logo-lg" />
                    <img src="/new-connectify/assets/images/logo.png" alt="" class="logo logo-sm" />
                </a>
            </div>
            <div class="navbar-content">
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Navigation</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-airplay"></i></span>
                            <span href="dashboard.php" class="nxl-mtext">Dashboard</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="dashboard.php">Main Dashboard</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-cast"></i></span>
                            <span class="nxl-mtext">Reports</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="./reports/report-data-list.php">Abnormal Report</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="reports-leads.html">Daily Target Report</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-layout"></i></span>
                            <span class="nxl-mtext">Library</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="widgets-lists.html">Models</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="widgets-tables.html">Error Code</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-send"></i></span>
                            <span class="nxl-mtext">Applications</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="apps-chat.html">Chat</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="apps-email.html">Email</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="apps-tasks.html">Tasks</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="apps-notes.html">Notes</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="apps-storage.html">Storage</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="apps-calendar.html">Calendar</a></li>
                        </ul>
                    </li>
         
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-settings"></i></span>
                            <span class="nxl-mtext">Settings</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="settings-general.html">General</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-seo.html">SEO</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-tags.html">Tags</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-email.html">Email</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-tasks.html">Tasks</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-leads.html">Leads</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-support.html">Support</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-finance.html">Finance</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-gateways.html">Gateways</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-customers.html">Customers</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-localization.html">Localization</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-recaptcha.html">reCAPTCHA</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="settings-miscellaneous.html">Miscellaneous</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-power"></i></span>
                            <span class="nxl-mtext">Authentication</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <span class="nxl-mtext">Login</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>
                                <ul class="nxl-submenu">
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-login-cover.html">Cover</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-login-minimal.html">Minimal</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-login-creative.html">Creative</a></li>
                                </ul>
                            </li>
                            <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <span class="nxl-mtext">Register</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>
                                <ul class="nxl-submenu">
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-register-cover.html">Cover</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-register-minimal.html">Minimal</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-register-creative.html">Creative</a></li>
                                </ul>
                            </li>
                            <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <span class="nxl-mtext">Error-404</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>
                                <ul class="nxl-submenu">
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-404-cover.html">Cover</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-404-minimal.html">Minimal</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-404-creative.html">Creative</a></li>
                                </ul>
                            </li>
                            <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <span class="nxl-mtext">Reset Pass</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>
                                <ul class="nxl-submenu">
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-reset-cover.html">Cover</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-reset-minimal.html">Minimal</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-reset-creative.html">Creative</a></li>
                                </ul>
                            </li>
                            <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <span class="nxl-mtext">Verify OTP</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>
                                <ul class="nxl-submenu">
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-verify-cover.html">Cover</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-verify-minimal.html">Minimal</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-verify-creative.html">Creative</a></li>
                                </ul>
                            </li>
                            <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <span class="nxl-mtext">Maintenance</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>
                                <ul class="nxl-submenu">
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-maintenance-cover.html">Cover</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-maintenance-minimal.html">Minimal</a></li>
                                    <li class="nxl-item"><a class="nxl-link" href="./auth-maintenance-creative.html">Creative</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Others</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Servers Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="projects.html">Server</a></li>
                        </ul>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Users Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="customers.html">Users</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <!--! ================================================================ !-->
    <!--! [Start] Header !-->
    <!--! ================================================================ !-->
    <header class="nxl-header">
        <div class="header-wrapper">
            <!--! [Start] Header Left !-->
            <div class="header-left d-flex align-items-center gap-4">
                <!--! [Start] nxl-head-mobile-toggler !-->
                <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                    <div class="hamburger hamburger--arrowturn">
                        <div class="hamburger-box">
                            <div class="hamburger-inner"></div>
                        </div>
                    </div>
                </a>
                <!--! [Start] nxl-head-mobile-toggler !-->
                <!--! [Start] nxl-navigation-toggle !-->
                <div class="nxl-navigation-toggle">
                    <a href="javascript:void(0);" id="menu-mini-button">
                        <i class="feather-align-left"></i>
                    </a>
                    <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                        <i class="feather-arrow-right"></i>
                    </a>
                </div>
                <!--! [End] nxl-navigation-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu-toggle !-->
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                        <i class="feather-align-left"></i>
                    </a>
                </div>
                <!--! [End] nxl-lavel-mega-menu-toggle !-->
                <!--! [Start] nxl-lavel-mega-menu !-->
                <div class="nxl-drp-link nxl-lavel-mega-menu">
                    <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                        <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                            <i class="feather-arrow-left me-2"></i>
                            <span>Back</span>
                        </a>
                    </div>
                </div>
                <!--! [End] nxl-lavel-mega-menu !-->
            </div>
            <!--! [End] Header Left !-->

            <!--! [Start] Header Right !-->
            <div class="header-right ms-auto">
                <div class="d-flex align-items-center">
                    <div class="dropdown nxl-h-item nxl-header-search">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="feather-search"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-search-dropdown">
                            <div class="input-group search-form">
                                <span class="input-group-text">
                                    <i class="feather-search fs-6 text-muted"></i>
                                </span>
                                <input type="text" class="form-control search-input-field" placeholder="Search...." />
                                <span class="input-group-text">
                                    <button type="button" class="btn-close"></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="nxl-h-item d-none d-sm-flex">
                        <div class="full-screen-switcher">
                            <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                                <i class="feather-maximize maximize"></i>
                                <i class="feather-minimize minimize"></i>
                            </a>
                        </div>
                    </div>
                    <div class="nxl-h-item dark-light-theme">
                        <a href="javascript:void(0);" class="nxl-head-link me-0 dark-button">
                            <i class="feather-moon"></i>
                        </a>
                        <a href="javascript:void(0);" class="nxl-head-link me-0 light-button" style="display: none">
                            <i class="feather-sun"></i>
                        </a>
                    </div>
                    <div class="dropdown nxl-h-item">
                        <a class="nxl-head-link me-3" data-bs-toggle="dropdown" href="#" role="button" data-bs-auto-close="outside">
                            <i class="feather-bell"></i>
                            <span id="notifCount" class="badge bg-danger nxl-h-badge">0</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-notifications-menu">
                            <div class="d-flex justify-content-between align-items-center notifications-head">
                                <h6 class="fw-bold text-dark mb-0">Notifications</h6>
                            </div>
                            <div id="notifList"></div>
                            <!-- <div class="notifications-item">
                                <img src="/new-connectify/assets/images/avatar/41.png" alt="" class="rounded me-3 border" />
                                <div class="notifications-desc">
                                    <a href="javascript:void(0);" class="font-body text-truncate-2-line"> <span class="fw-semibold text-dark">Archie Cantones</span> Don't forget to pickup Jeremy after school!</a>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="notifications-date text-muted border-bottom border-bottom-dashed">53 minutes ago</div>
                                        <div class="d-flex align-items-center float-end gap-2">
                                            <a href="javascript:void(0);" class="d-block wd-8 ht-8 rounded-circle bg-gray-300" data-bs-toggle="tooltip" title="Make as Read"></a>
                                            <a href="javascript:void(0);" class="text-danger" data-bs-toggle="tooltip" title="Remove">
                                                <i class="feather-x fs-12"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="text-center notifications-footer">
                                <a href="javascript:void(0);" class="fs-13 fw-semibold text-dark">All Notifications</a>
                            </div>
                        </div>
                    </div>
                    <div class="dropdown nxl-h-item">
                        <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                            <img src="/new-connectify/assets/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar me-0" />
                        </a>
                        <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                            <div class="dropdown-header">
                                <div class="d-flex align-items-center">
                                    <img src="/new-connectify/assets/images/avatar/1.png" alt="user-image" class="img-fluid user-avtar" />
                                    <div>
                                        <h6 class="text-dark mb-1"><span><?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?></span></h6>
                                        <p class="fs-12 fw-small text-muted"><?= htmlspecialchars($_SESSION['department_name']) ?></p>
                                    </div>
                                </div>
                            </div>
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">
                                <i class="feather-user"></i>
                                <span>Profile</span>
                            </a>
                            <a href="javascript:void(0);" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editSettingModal">
                                <i class="feather-settings"></i>
                                <span>Settings</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#logoutModal">
                                <i class="feather-log-out"></i>
                                <span>Logout</span>
                            </a>    
                        </div>
                    </div>
                </div>
            </div>
            <!--! [End] Header Right !-->
        </div>
    </header>
    <!--! ================================================================ !-->
    <!--! [End] Header !-->
    <!--! ================================================================ !-->
    <!--! ================================================================ !-->
    <!--! [Start] Main Content !-->
    <!--! ================================================================ !-->
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <div class="page-header-title">
                        <h5 class="m-b-10">Dashboard</h5>
                    </div>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">Dashboard</li>
                    </ul>
                </div>
                <div class="page-header-right ms-auto">
                    <div class="page-header-right-items">
                        <div class="d-flex d-md-none">
                            <a href="javascript:void(0)" class="page-header-right-close-toggle">
                                <i class="feather-arrow-left me-2"></i>
                                <span>Back</span>
                            </a>
                        </div>
                    </div>
                    <div class="d-md-none d-flex align-items-center">
                        <a href="javascript:void(0)" class="page-header-right-open-toggle">
                            <i class="feather-align-right fs-20"></i>
                        </a>
                    </div>
                </div>
            </div>
            <!-- [ page-header ] end -->
             
            <!-- [ Main Content ] start -->
             <!-- Main Content-->
            <div class="main-content">
                <div class="col-xxl-12 col-md-6">
                    <!-- <h4 class="h3 mb-8 text-gray-800">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?></h4> -->
                    <h3 class="h3 ">Welcome to Connectify</h3>
                    <p>This is used for reporting and monitoring data</p>
                    <hr class="my-12">
                </div>
                <div class="row">
                    <!-- Cards -->
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-cast"></i>
                                        </div>
                                        <div>
                                            <div id="totalReports" class="fs-1 fw-bold text-dark"><span class="counter">0</span></div>
                                            <h3 id="mycardreport" class="fs-13 fw-semibold text-truncate-1-line">
                                                <a class="nxl-link" href="/new-connectify/pages/reports/report-data-list.php">Abnormal Report</a>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-layout"></i>
                                        </div>
                                        <div>
                                            <div id="totalTargetReports" class="fs-1 fw-bold text-dark"><span class="counter">0</span></div>
                                            <h3 id="mycardreport" class="fs-13 fw-semibold text-truncate-1-line">
                                                <a class="nxl-link" href="/new-connectify/pages/reports/daily-target-report.php">Daily Target Report</a>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-activity"></i>
                                        </div>
                                        <div>
                                            <div id="totalErrorCode" class="fs-1 fw-bold text-dark"><span class="counter">0</span></div>
                                            <h3 id="mycardreport" class="fs-13 fw-semibold text-truncate-1-line">
                                                <a class="nxl-link" href="/new-connectify/pages/library/error-code-list.php">Error Code</a>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-3 col-md-6">
                        <div class="card stretch stretch-full">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-4">
                                    <div class="d-flex gap-4 align-items-center">
                                        <div class="avatar-text avatar-lg bg-gray-200">
                                            <i class="feather-users"></i>
                                        </div>
                                        <div>
                                            <div id="totalUsers" class="fs-1 fw-bold text-dark"><span class="counter">0</span></div>
                                            <h3 class="fs-13 fw-semibold text-truncate-1-line">Users</h3>
                                        </div>
                                    </div>
                                    <!-- <a href="javascript:void(0);" class="">
                                        <i class="feather-more-vertical"></i>
                                    </a> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="col-xxl-6">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Abnormal Report Chart</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                    <!-- <div class="dropdown">
                                        <a href="javascript:void(0);" class="avatar-text avatar-sm" data-bs-toggle="dropdown" data-bs-offset="25, 25">
                                            <div data-bs-toggle="tooltip" title="Options">
                                                <i class="feather-more-vertical"></i>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-at-sign"></i>New</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-calendar"></i>Event</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-bell"></i>Snoozed</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-trash-2"></i>Deleted</a>
                                            <div class="dropdown-divider"></div>
                                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-settings"></i>Settings</a>
                                            <a href="javascript:void(0);" class="dropdown-item"><i class="feather-life-buoy"></i>Tips & Tricks</a>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <!-- <div id="payment-records-chart"></div> -->
                                <canvas id="abnormalReportsChart"></canvas>
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-6">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Daily Target Report Chart</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <!-- <div id="daily-target-chart"></div> -->
                                <canvas id="targetReportsChart"></canvas>
                                <div id="chartTooltip"></div>
                            </div>
                        </div>
                    </div>
                    <!-- [Abnormal chart] end -->
                </div>
            </div>
            <!-- [ Main Content ] end -->
            <!-- [ Main Content ] end -->
        </div>
        <?php
        require_once './layout/footer.php';
        ?>
    </main>
    <?php
    require_once './layout/footer.php';
    require_once './layout/theme.php';
    ?>

    
    <div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ready to Leave?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Select "Logout" below if you are ready to end your current session.
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="../logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileModalLabel">My Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Name:</strong> <span id="profileName"></span></li>
                        <li class="list-group-item"><strong>Work ID:</strong> <span id="profileWorkId"></span></li>
                        <li class="list-group-item"><strong>Department:</strong> <span id="profileDept"></span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editSettingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editSettingLabel">Setting</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="message-edit-container"></div>
                    <form id="settingForm" class="row g-3">
                        <div class="col-md-6">
                            <label>New Password</label>
                            <input type="password" id="settingPassword" name="password" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label>Confirm Password</label>
                            <input type="password" id="settingPasswordConfirm" name="password_confirm" class="form-control">
                        </div>
                        <div class="modal-footer col-12 d-flex justify-content-center mt-4">
                            <button type="button" id="cancelSetting" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="saveSetting" class="btn btn-success px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/new-connectify/assets/vendors/js/vendors.min.js"></script>
    <script src="/new-connectify/assets/vendors/js/daterangepicker.min.js"></script>
    <script src="/new-connectify/assets/vendors/js/apexcharts.min.js"></script>
    <script src="/new-connectify/assets/vendors/js/circle-progress.min.js"></script>
    <script src="/new-connectify/assets/js/common-init.min.js"></script>
    <script src="/new-connectify/assets/js/dashboard-init.min.js"></script>
    <script src="/new-connectify/assets/js/theme-customizer-init.min.js"></script>

    <script src="/new-connectify/assets/public/vendor/chart.js/Chart.min.js"></script>
    <script src="./js/dashboard.js"></script>
      <script src="/new-connectify/assets/public/vendor/DataTables/datatables.min.js"></script>
    <script>
        setInterval(() => {
            fetch("/new-connectify/update_activity.php");
        }, 60000);
    </script>

    <script>
        const CURRENT_USER_NAME = "<?php echo $_SESSION['name']; ?>";
        const CURRENT_USER_WORK_ID = "<?php echo $_SESSION['work_id']; ?>";
        const CURRENT_USER_DEPT_ID = "<?php echo $_SESSION['department_id']; ?>";
        const CURRENT_USER_DEPT = "<?php echo $_SESSION['department_name']; ?>";
        const CURRENT_USER_ROLE = "<?php echo $_SESSION['role_name']; ?>";
        const CURRENT_USER_ROLE_ID = "<?php echo $_SESSION['role_id']; ?>";
    </script>
    <script>
        function timeAgo(dateString) {
            const time = new Date(dateString);
            const now = new Date();
            const seconds = Math.floor((now - time) / 1000);

            const intervals = {
                year: 31536000,
                month: 2592000,
                day: 86400,
                hour: 3600,
                minute: 60
            };

            for (let key in intervals) {
                const value = Math.floor(seconds / intervals[key]);
                if (value >= 1) {
                    return `${value} ${key}${value > 1 ? 's' : ''} ago`;
                }
            }
            return "just now";
        }

        function loadNotifications() {
            fetch("/new-connectify/controllers/NotificationController.php?type=notification")
                .then(res => res.json())
                .then(data => {
                    const notifList  = document.getElementById("notifList");
                    const notifCount = document.getElementById("notifCount");

                    notifList.innerHTML = "";

                    if (!data.success || data.data.length === 0) {
                        notifList.innerHTML = `
                            <div class="text-center text-muted py-4">
                                No notifications
                            </div>
                        `;
                        notifCount.textContent = 0;
                        return;
                    }

                    notifCount.textContent = data.data.length;

                    data.data.forEach(n => {
                        notifList.innerHTML += `
                            <div class="notifications-item">
                                <img src="/new-connectify/assets/images/icons/4.png"
                                    class="rounded me-3 border" width="40" height="40">

                                <div class="notifications-desc">
                                    <a href="#" class="font-body text-truncate-3-line">
                                        ${n.message}
                                    </a>
                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                        <div class="notifications-date text-muted">
                                            ${timeAgo(n.created_at)}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                })
                .catch(err => console.error("Error loading notifications:", err));
        }
        document.addEventListener("DOMContentLoaded", loadNotifications);
        setInterval(loadNotifications, 8000);
    </script>
     <script>
        $(window).on('load', function() {
            $('#preloader').fadeOut('slow', function() {
                $(this).remove();
            });

            getTotalUsers();
            getTotalReports();
            getTotalTargetReports();
            getTotalErrorCode();
        });

        function getTotalUsers() {
            $.ajax({
                url: '/new-connectify/controllers/UserController.php?type=total',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalUsers').text(response.total);
                    } else {
                        $('#totalUsers').text('-');
                    }
                },
                error: function() {
                    $('#totalUsers').text('-');
                }
            });
        }

        function getTotalReports() {
            $.ajax({
                url: '/new-connectify/controllers/ReportController.php?type=total',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalReports').text(response.total);
                    } else {
                        $('#totalReports').text('-');
                    }
                },
                error: function() {
                    $('#totalReports').text('-');
                }
            });
        }

        function getTotalErrorCode() {
            $.ajax({
                url: '/new-connectify/controllers/ErrorCodeController.php?type=total',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalErrorCode').text(response.total);
                    } else {
                        $('#totalErrorCode').text('-');
                    }
                },
                error: function() {
                    $('#totalErrorCode').text('-');
                }
            });
        }

        function getTotalTargetReports() {
            $.ajax({
                url: '/new-connectify/controllers/DailyTargetReportController.php?type=total',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalTargetReports').text(response.total);
                    } else {
                        $('#totalTargetReports').text('-');
                    }
                },
                error: function() {
                    $('#totalTargetReports').text('-');
                }
            });
        }
    </script>
    <script>
        $.ajax({
            url: '/new-connectify/controllers/ReportController.php?type=total-by-model',
            type: 'GET',
            dataType: 'json',
            success: function(json) {
                if (json.success && Array.isArray(json.data)) {
                    const labels = json.data.map(item => item.model_name);
                    const totalReports = json.data.map(item => parseInt(item.total_report));

                    const ctx = document.getElementById('abnormalReportsChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Total Reports',
                                data: totalReports,
                                backgroundColor: 'rgba(20, 160, 67, 0.9)',
                                borderColor: 'rgb(42, 223, 147)',
                                borderWidth: 1,
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    precision: 0
                                }
                            },
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                        }
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error("Chart data fetch error:", error);
            }
        });

        $.ajax({
            url: '/new-connectify/controllers/DailyTargetReportController.php?type=target-report-chart',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.success || !Array.isArray(res.data)) return;

                const data = res.data;
                data.sort((a, b) => new Date(a.date) - new Date(b.date));

                const labels = [...new Set(data.map(d => d.date))];

                const models = {};
                data.forEach(d => {
                    if (!models[d.model_name]) models[d.model_name] = {};
                    models[d.model_name][d.date] = d.uph_status_name;
                });

                const statusCategories = ['Not Target', 'Not Running', 'Target'];

                const modelColors = [
                    // '#1f77b4', 
                    // '#ff7f0e', 
                    '#2ca02c', 
                    // '#d62728', 
                    // '#9467bd', 
                    // '#8c564b', 
                    // '#e377c2', 
                    // '#7f7f7f',  
                    // '#bcbd22', 
                    // '#17becf'
                ];

                const modelNames = Object.keys(models);
                const datasets = modelNames.map((modelName, index) => {
                    const yValues = labels.map(date => {
                        const status = models[modelName][date] || 'Not Running';
                        return statusCategories.indexOf(status);
                    });

                    const pointColors = labels.map(date => {
                        const status = models[modelName][date] || 'Not Running';
                        switch (status.toLowerCase()) {
                            case 'target':
                                return 'green';
                            case 'not target':
                                return 'red';
                            case 'not running':
                                return 'yellow';
                            default:
                                return 'gray';
                        }
                    });

                    return {
                        label: modelName,
                        data: yValues,
                        borderColor: modelColors[index % modelColors.length],
                        borderWidth: 2,
                        pointRadius: 6,
                        pointBackgroundColor: pointColors,
                        pointBorderColor: pointColors,
                        fill: false,
                        tension: 0.2,
                        showLine: false
                    };
                });

                const ctx = document.getElementById('targetReportsChart').getContext('2d');

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        layout: {
                            padding: {
                                right: 105
                            }
                        },
                        scales: {
                            y: {
                                type: 'category',
                                labels: statusCategories,
                                offset: true
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const modelName = ctx.dataset.label;
                                        const date = labels[ctx.dataIndex];
                                        const status = models[modelName][date] || 'Not Running';
                                        return modelName + ': ' + status;
                                    }
                                }
                            },
                            legend: {
                                position: 'top', // legend model tetap di atas
                                labels: {
                                    usePointStyle: true, // kotak kecil seperti titik
                                }
                            },
                        },

                    },
                    plugins: [{
                        id: 'statusRightLegend',
                        afterDraw: function(chart) {
                            const ctx = chart.ctx;
                            ctx.save();
                            ctx.font = '12px roboto';
                            ctx.textAlign = 'left';
                            ctx.textBaseline = 'middle';

                            const right = chart.chartArea.right + 20;
                            const top = chart.chartArea.top;

                            const statusColors = {
                                'Target': 'green',
                                'Not Target': 'red',
                                'Not Running': 'yellow'
                            };

                            let i = 0;
                            Object.keys(statusColors).forEach(status => {
                                ctx.fillStyle = statusColors[status];
                                ctx.fillRect(right, top + i * 20, 12, 12);

                                ctx.fillStyle = '#000';
                                ctx.fillText(status, right + 16, top + i * 20 + 6);
                                i++;
                            });

                            ctx.restore();
                        }
                    }]
                });
            }
        });

       
    </script>
</body>

</html>