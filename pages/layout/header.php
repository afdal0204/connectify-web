<style>
#notifList {
    max-height: 300px;  
    overflow-y: auto;   
    padding-right: 10px; 
    padding-bottom: 10px; 
}
.notifications-footer {
    margin-top: 10px;
}
</style>

<header class="nxl-header">
    <div class="header-wrapper">
        <div class="header-left d-flex align-items-center gap-4">
            <a href="javascript:void(0);" class="nxl-head-mobile-toggler" id="mobile-collapse">
                <div class="hamburger hamburger--arrowturn">
                    <div class="hamburger-box">
                        <div class="hamburger-inner"></div>
                    </div>
                </div>
            </a>
            <div class="nxl-navigation-toggle">
                <a href="javascript:void(0);" id="menu-mini-button">
                    <i class="feather-align-left"></i>
                </a>
                <a href="javascript:void(0);" id="menu-expend-button" style="display: none">
                    <i class="feather-arrow-right"></i>
                </a>
            </div>
            <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                <a href="javascript:void(0);" id="nxl-lavel-mega-menu-open">
                    <i class="feather-align-left"></i>
                </a>
            </div>
            <div class="nxl-drp-link nxl-lavel-mega-menu">
                <div class="nxl-lavel-mega-menu-toggle d-flex d-lg-none">
                    <a href="javascript:void(0)" id="nxl-lavel-mega-menu-hide">
                        <i class="feather-arrow-left me-2"></i>
                        <span>Back</span>
                    </a>
                </div>
            </div>
        </div>
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
                <!-- <div class="nxl-h-item d-none d-sm-flex">
                    <div class="full-screen-switcher">
                        <a href="javascript:void(0);" class="nxl-head-link me-0" onclick="$('body').fullScreenHelper('toggle');">
                            <i class="feather-maximize maximize"></i>
                            <i class="feather-minimize minimize"></i>
                        </a>
                    </div>
                </div> -->
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
                         <div id="notifList" style="max-height: 500px; overflow-y: auto; padding-right: 10px;"></div>
                        <!-- <div id="notifList"></div> -->
                        <div class="text-center notifications-footer">
                            <!-- <a href="javascript:void(0);" class="fs-13 fw-semibold text-dark">All Notifications</a> -->
                        </div>
                    </div>
                </div>
                <div class="dropdown nxl-h-item">
                    <a href="javascript:void(0);" data-bs-toggle="dropdown" role="button" data-bs-auto-close="outside">
                        <img src="/connectify-web/assets/images/avatar/auth-user.png" alt="user-image" class="img-fluid user-avtar me-0" />
                    </a>
                    <div class="dropdown-menu dropdown-menu-end nxl-h-dropdown nxl-user-dropdown">
                        <div class="dropdown-header">
                            <div class="d-flex align-items-center">
                                <img src="/connectify-web/assets/images/avatar/auth-user.png" alt="user-image" class="img-fluid user-avtar" />
                                
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
    </div>
</header>

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
                <a class="btn btn-primary" href="/connectify-web/logout.php">Logout</a>
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

<script src="/connectify-web/assets/public/vendor/DataTables/jquery.dataTables.min.js"></script>
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
        fetch("/connectify-web/controllers/NotificationController.php?type=notification")
            .then(res => res.json())
            .then(data => {
                const notifList = document.getElementById("notifList");
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
                                <img src="/connectify-web/assets/images/icons/4.png"
                                    class="rounded me-3 border" width="40" height="40">

                                <div class="notifications-desc">
                                    <a href="#" class="font-body text-truncate-6-line">
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


