<style>
    .text-badge-wrapper {
        flex-wrap: wrap;
        gap: 0.25rem;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .nxl-arrow i {
        vertical-align: middle;
        font-size: 1rem;
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
                        <!-- <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/analytics.php">Analytics <span class="badge bg-soft-success text-success">NEW</span></a></li> -->
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/analytics.php">Analytics</a></li>
                    </ul>
                </li>
                <li class="nxl-item nxl-hasmenu">
                    <a href="javascript:void(0);" class="nxl-link">
                        <span class="nxl-micon"><i class="feather-cast"></i></span>
                        <span class="nxl-mtext">Multi-Dept Database</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                    </a>
                    <ul class="nxl-submenu">
                        <?php if ($role_id == 1 || $deptRemark == 'PE' || $deptRemark == 'Management') : ?>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/report-data-list.php">PE Abnormal Reports</a></li>
                        <?php endif; ?>
                        <?php if ($role_id == 1 || $deptRemark == 'QA' || $deptRemark == 'Management') : ?>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/qa-report.php">QA Reports</a></li>
                        <?php endif; ?>
                        <?php if ($role_id == 1 || $deptRemark == 'SQE' || $deptRemark == 'Management') : ?>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/sqe-report.php">SQE Reports</a></li>
                        <?php endif; ?>
                        <?php if ($role_id == 1 || $deptRemark == 'PD' || $deptRemark == 'Management') : ?>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/pd-report.php">PD Production Data</a></li>
                        <?php endif; ?>
                        <?php if ($role_id == 1 || $deptRemark == 'FE' || $deptRemark == 'Management') : ?>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/fe-report.php">FE Fixture Reports</a></li>
                        <?php endif; ?>
                        <?php if ($role_id == 1 || $deptRemark == 'FME' || $deptRemark == 'Management') : ?>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/fme-report.php">FME Reports</a></li>
                        <?php endif; ?>
                        <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/daily-target-report.php">Daily Target Report</a></li>
                    </ul>
                </li>
                <?php if ($role_id == 1 || $deptRemark == 'PE' || $deptRemark == 'Management') : ?>
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-clock"></i></span>
                            <span class="nxl-mtext"> Line Report (Shift)</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/line-report/sec1.php">Sec 1</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/line-report/sec2.php">Sec 2</a></li>
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/reports/line-report/sec3.php">Sec 3</a></li>
                        </ul>
                    </li>
                <?php endif; ?>
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
                <?php if ($role_id == 1 || $role_id == 4 || $role_id == 5 || $role_id == 6 || $deptRemark == 'Management') : ?>
                    <li class="nxl-item">
                        <a href="/connectify-web/pages/server/server-list.php" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-server"></i></span>
                            <span class="nxl-mtext">Servers Info</span>
                        </a>
                    </li>
                    <!-- <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-server"></i></span>
                            <span class="nxl-mtext">Servers Info</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/server/server-list.php">Server</a></li>
                        </ul>
                    </li> -->
                <?php endif; ?>
            </ul>
            <!-- OTHER(Only Admin) -->
            <?php if ($role_id == 1): ?>
                <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Admin Only</label>
                    </li>

                    <li class="nxl-item">
                        <a href="/connectify-web/pages/user-action/user-list.php" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">Users</span>
                        </a>
                    </li>

                    <li class="nxl-item">
                        <a href="/connectify-web/pages/user-action/department-list.php" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-briefcase"></i></span>
                            <span class="nxl-mtext">Departments</span>
                        </a>
                    </li>
                </ul>
                <!-- <ul class="nxl-navbar">
                    <li class="nxl-item nxl-caption">
                        <label>Admin Only</label>
                    </li>
                    <li class="nxl-item nxl-hasmenu">
                    <li class="nxl-item nxl-hasmenu">
                        <a href="javascript:void(0);" class="nxl-link">
                            <span class="nxl-micon"><i class="feather-users"></i></span>
                            <span class="nxl-mtext">System Management</span><span class="nxl-arrow"><i class="feather-chevron-right"></i></span>
                        </a>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/user-action/user-list.php">Users</a></li>
                        </ul>
                        <ul class="nxl-submenu">
                            <li class="nxl-item"><a class="nxl-link" href="/connectify-web/pages/user-action/department-list.php">Departments</a></li>
                        </ul>
                    </li>
                </ul> -->
            <?php endif; ?>
        </div>
    </div>
</nav>