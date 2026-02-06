<style>
    .text-badge-wrapper {
        flex-wrap: wrap;       /* supaya teks panjang bisa wrap */
        gap: 0.25rem;          /* jarak teks dan badge */
    }

    .text-truncate {
        white-space: nowrap;   /* atau gunakan wrap tergantung kebutuhan */
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .nxl-arrow i {
        vertical-align: middle;  /* memaksa icon sejajar tengah dengan teks */
        font-size: 1rem;         /* sesuaikan dengan font teks */
    }


</style>
<nav class="nxl-navigation">
    <div class="navbar-wrapper">
        <div class="m-header">
            <a href="/connectify-web/pages/dashboard.php" class="b-brand">
                <img src="/connectify-web/assets/images/logo21.png" alt="connectify logo" class="logo logo-lg" />
                <img src="/connectify-web/assets/images/logo.png" alt="connectify logo" class="logo logo-sm" />
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
                        <span href="/connectify-web/pages/dashboard.php" class="nxl-mtext">Dashboard</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/dashboard.php">Main Dashboard</a></li>
                    </ul>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/analytics.php">Analytics <span class="badge bg-soft-success text-success">NEW</span></a></li>
                    </ul>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-cast"></i></span>
                        <span class="nxl-mtext">Reports</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/report-data-list.php">Abnormal Report</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/daily-target-report.php">Daily Target Report</a></li>
                        <li class="nxl-item nxl-hasmenu">
                                <a href="javascript:void(0);" class="nxl-link">
                                    <!-- <span class="nxl-micon"><i class="feather-file-text"></i></span> -->
                                    Line Report (Shift)<span class="badge bg-soft-success text-success ms-2">NEW</span>  
                                    <span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                                </a>

                            <!-- SUBMENU LEVEL 2 -->
                            <ul class="nxl-submenu">
                                <li class="nxl-item">
                                    <a class="nxl-link" href="/connectify-web/pages/reports/line-report/sec1.php">
                                        Sec 1 
                                    </a>
                                </li>
                                <li class="nxl-item">
                                    <a class="nxl-link" href="/connectify-web/pages/reports/line-report/sec2.php">
                                        Sec 2
                                    </a>
                                </li>
                                <li class="nxl-item">
                                    <a class="nxl-link" href="/connectify-web/pages/reports/line-report/sec3.php">
                                        Sec 3
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-layout"></i></span>
                        <span class="nxl-mtext">Library</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/library/model-list.php">Models</a></li>
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/library/error-code-list.php">Error Code</a></li>
                    </ul>
                </li>
            </ul>
            
            <?php if ($role_id == 1 || $role_id == 4 || $role_id == 5 || $role_id == 6): ?>
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Others</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-server"></i></span>
                            <span class="nxl-mtext">Servers Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/server/server-list.php">Server</a></li>
                        </ul>
                    </li>
                    <?php if ($role_id == 1): ?>
                        <li class="nxl-item nxl-hasmenu">
                            <a href="javascript:void(0);" class="nxl-link">
                                <span class="nxl-micon"><i class="feather-users"></i></span>
                                <span class="nxl-mtext">Users Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                            </a>
                            <ul class="nxl-submenu">
                                <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/user-action/user-list.php">Users</a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>