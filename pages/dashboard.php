<?php
include '../config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: /connectify-web/login.php?redirect={$redirect}");
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

$role_id = $_SESSION['role_id'] ?? 'Guest';
$department_id = $_SESSION['department_id'];
$deptRemark = $_SESSION['deptRemark'];
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
    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/daterangepicker.min.css" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css" />
    <link href="/connectify-web/assets/public/vendor/DataTables/datatables.min.css" rel="stylesheet">

    <link rel="stylesheet" href="/connectify-web/assets/css/flatpickr.min.css">
    <script src="/connectify-web/assets/js/flatpickr.min.js"></script>
    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> -->
    <
        </head>

<body>
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

        .flatpickr-input {
            background-color: #fff !important;
        }
    </style>
    <?php
    require_once './layout/sidebar.php';
    require_once './layout/header.php';
    ?>
    <main class="nxl-container">
        <div class="nxl-content">
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

            <!-- Main Content-->
            <div class="main-content">
                <div class="col-xxl-12 col-md-6">
                    <div class="card-body">
                        <h3 class="fw-bold mb-1">
                            👋 Welcome back,
                            <span class="text-primary">
                                <?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?>
                            </span>
                        </h3>
                        <p class="text-muted mb-0">
                            Here's what's happening in Connectify today.
                        </p>
                    </div>
                    <!-- <h4 class="h3 mb-8 text-gray-800">Hi, <?= htmlspecialchars($_SESSION['name'] ?? 'Guest') ?></h4>
                    <h3 class="h3 ">Welcome to Connectify</h3>
                    <p>This is used for reporting and monitoring data</p> -->
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
                                            <i class="feather-bar-chart-2"></i>
                                        </div>
                                        <div>
                                            <div id="totalReports" class="fs-1 fw-bold text-dark"><span class="counter">0</span></div>
                                            <h3 id="mycardreport1" class="fs-13 fw-semibold text-truncate-1-line">
                                                <!-- <a class="nxl-link" href="/connectify-web/pages/reports/report-data-list.php">Abnormal Report</a> -->
                                                Abnormal Reports
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
                                            <i class="feather-clipboard"></i>
                                        </div>
                                        <div>
                                            <div id="totalLineReports" class="fs-1 fw-bold text-dark"><span class="counter">0</span></div>
                                            <h3 id="mycardreport1" class="fs-13 fw-semibold text-truncate-1-line">
                                                Line Reports per Shift
                                                <!-- <a class="nxl-link" href="#">Line Report</a> -->
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
                                            <h3 id="mycardreport1" class="fs-13 fw-semibold text-truncate-1-line">
                                                Daily Target Reports
                                                <!-- <a class="nxl-link" href="#">Daily Target Report</a> -->
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
                                            <h3 id="mycardreport1" class="fs-13 fw-semibold text-truncate-1-line">
                                                Error Code
                                                <!-- <a class="nxl-link" href="#">Error Code</a> -->
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Chart -->
                        <div class="col-lg-9">
                            <div class="card stretch stretch-full h-60">
                                <div class="card-header">
                                    <h5 class="card-title">Daily Target Report</h5>
                                    <div class="card-header-action d-flex gap-2">
                                        <a class="btn btn-light-brand"
                                            data-bs-toggle="modal"
                                            data-bs-target="#filterModal">
                                            <i class="feather-filter me-2"></i>
                                            <span>Filter</span>
                                        </a>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="targetReportsChart"></div>
                                    <div id="chartTooltip"></div>
                                </div>
                            </div>
                        </div>
                        <!-- Recent Activity -->
                        <div class="col-lg-3">
                            <div class="card stretch stretch-full h-60">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="feather-bell text-warning me-2"></i>
                                        Recent Activity
                                    </h5>
                                </div>

                                <div class="card-body p-0">
                                    <div id="activityTimeline"
                                        style="max-height:500px;overflow-y:auto">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Daily Target Report</h5>
                                <div class="card-header-action d-flex gap-2">
                                    <a class="btn btn-light-brand" data-bs-toggle="modal" data-bs-target="#filterModal" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                        <i class="feather-filter me-2"></i>
                                        <span>Filter</span>
                                    </a>
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div id="targetReportsChart"></div>
                                <div id="chartTooltip"></div>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
        </div>

        <?php
        require_once './layout/footer.php';
        ?>

    </main>

    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">From Date</label>
                            <input type="date"
                                id="fromDate"
                                class="form-control form-control-sm"
                                max="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-semibold">To Date</label>
                            <input type="date"
                                id="toDate"
                                class="form-control form-control-sm"
                                max="<?= date('Y-m-d') ?>">
                        </div>
                        <!-- <div id="selectedRange" class="small text-primary mt-2"></div> -->
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-semibold mb-2">
                            Select Model
                        </label>

                        <div id="modelDropdown"
                            class="border rounded p-3 d-flex flex-wrap gap-3"
                            style="max-height:180px; overflow-y:auto;">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" id="applyFilter">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    require_once './layout/footer.php';
    require_once './layout/theme.php';
    ?>

    <script src="/connectify-web/assets/vendors/js/vendors.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/daterangepicker.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/apexcharts.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/circle-progress.min.js"></script>
    <script src="/connectify-web/assets/js/common-init.min.js"></script>
    <script src="/connectify-web/assets/js/dashboard-init.min.js"></script>
    <script src="/connectify-web/assets/js/theme-customizer-init.min.js"></script>

    <script src="/connectify-web/assets/public/vendor/chart.js/Chart.min.js"></script>

    <script src="./js/dashboard.js"></script>
    <script src="/connectify-web/assets/public/vendor/DataTables/datatables.min.js"></script>
    <script>
        setInterval(() => {
            fetch("/connectify-web/update_activity.php");
        }, 60000);

        setTimeout(function() {
            alert("Session expired after 7200 seconds. You will be logged out, please login again!");
            window.location.href = "/connectify-web/logout.php";
        }, 7200 * 1000);
    </script>
    <script>
        const today = new Date();
        const fromPicker = flatpickr("#fromDate", {
            dateFormat: "Y-m-d",
            maxDate: today,
            onChange: function(selectedDates, dateStr, instance) {
                toPicker.set("minDate", dateStr);
            }
        });

        const toPicker = flatpickr("#toDate", {
            dateFormat: "Y-m-d",
            maxDate: today,
            onChange: function(selectedDates, dateStr, instance) {
                fromPicker.set("maxDate", dateStr);
            }
        });
    </script>
    <script>
        $(window).on('load', function() {
            $('#preloader').fadeOut('slow', function() {
                $(this).remove();
            });

            getTotalUsers();
            getTotalReports();
            getTotalLineReports()
            getTotalTargetReports();
            getTotalErrorCode();
            loadNotifications();
        });

        function getTotalUsers() {
            $.ajax({
                url: '/connectify-web/controllers/UserController.php?type=total',
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
                url: '/connectify-web/controllers/ReportController.php?type=total',
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

        function getTotalLineReports() {
            $.ajax({
                url: '/connectify-web/controllers/LineReportController.php?type=total',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#totalLineReports').text(response.total);
                    } else {
                        $('#totalLineReports').text('-');
                    }
                },
                error: function() {
                    $('#totalLineReports').text('-');
                }
            });
        }

        function getTotalErrorCode() {
            $.ajax({
                url: '/connectify-web/controllers/ErrorCodeController.php?type=total',
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
                url: '/connectify-web/controllers/DailyTargetReportController.php?type=total',
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

        function loadNotifications() {
            fetch("/connectify-web/controllers/NotificationController.php?type=notification")
                .then(res => res.json())
                .then(data => {

                    const timeline = document.getElementById("activityTimeline");

                    timeline.innerHTML = "";

                    if (!data.success || data.data.length === 0) {
                        timeline.innerHTML = `
                            <div class="text-center text-muted py-4">
                                No recent activity
                            </div>
                        `;
                        return;
                    }

                    data.data.slice(0, 10).forEach(n => {
                        timeline.innerHTML += `
                            <div class="d-flex p-3 border-bottom">
                                <div class="me-3">
                                    <div class="avatar-text bg-primary text-white rounded-circle">
                                        <i class="feather-bell"></i>
                                    </div>
                                </div>

                                <div>
                                    <div class="fw-semibold">
                                        ${n.message}
                                    </div>

                                    <small class="text-muted">
                                        ${timeAgo(n.created_at)}
                                    </small>
                                </div>
                            </div>
                        `;
                    });
                });
        }
    </script>

    <!-- <script>
        let chart;
        let allSeries = [];
        let allModels = [];
        let rawDataGlobal = [];

        $.ajax({
            url: '/connectify-web/controllers/DailyTargetReportController.php?type=target-report-chart',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.success) return;

                const rawData = res.data;

                const categories = [...new Set(rawData.map(item => item.date))].sort();
                const isoDates = categories.map(d => new Date(d).toISOString());

                allModels = [...new Set(rawData.map(item => item.model_name))].sort();

                const statusMap = {
                    "not target": 1,
                    "target": 2,
                    "not running": 0
                };

                const reverseStatusMap = {
                    1: "Not Target",
                    2: "Target",
                    0: "Not Running"
                };

                // 🔹 build ALL series
                allSeries = allModels.map(model => {
                    const data = isoDates.map((iso, idx) => {
                        const date = categories[idx];
                        const found = rawData.find(item =>
                            item.model_name === model &&
                            item.date === date
                        );
                        return found ? statusMap[found.uph_status_name.toLowerCase()] : 0;
                    });

                    return { name: model, data };
                });

                // 🔹 DEFAULT: hanya 3 garis pertama
                const defaultSeries = allSeries.slice(0, 3);

                var options = {
                    series: defaultSeries,
                    chart: {
                        type: 'area',
                        height: 400,
                        toolbar: { show: true }
                    },
                    stroke: {
                        width: 3,
                        curve: 'smooth'
                    },
                    markers: { size: 5 },
                    xaxis: {
                        title: {
                            text: 'Date'
                        },
                        type: 'datetime',
                        categories: isoDates,
                        labels: {
                            formatter: function(value) {
                                const date = new Date(value);
                                return date.toLocaleDateString('en-GB', {
                                    year: 'numeric',
                                    month: 'short',
                                    day: 'numeric'
                                });
                            }
                        }
                    },
                    yaxis: {
                        tickAmount: 2,
                        labels: {
                            formatter: val => reverseStatusMap[val] || val
                        },
                        title: {
                            text: '......'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: val => reverseStatusMap[val] || '-'
                        }
                    }
                };

                chart = new ApexCharts(
                    document.querySelector("#targetReportsChart"),
                    options
                );
                chart.render();

                initDefaultDate();
                initModelFilter();
            }
        });

        function initModelFilter() {
            const container = $('#modelDropdown');
            container.empty();

            allModels.forEach((model, idx) => {
                container.append(`
                    <div class="form-check">
                        <input class="form-check-input model-checkbox"
                            type="checkbox"
                            value="${model}"
                            id="model_${idx}">
                        <label class="form-check-label" for="model_${idx}">
                            ${model}
                        </label>
                    </div>
                `);
            });

            // default check 3 pertama
            $('.model-checkbox').slice(0, 3).prop('checked', true);
        }
        function initDefaultDate() {
            const today = new Date();
            const lastMonth = new Date();
            lastMonth.setMonth(today.getMonth() - 1);

            $('#toDate').val(today.toISOString().split('T')[0]);
            $('#fromDate').val(lastMonth.toISOString().split('T')[0]);
        }
        $('#applyFilter').on('click', function () {
            const selected = $('.model-checkbox:checked')
                .map(function () {
                    return this.value;
                }).get();

            chart.updateSeries(
                selected.length
                    ? allSeries.filter(s => selected.includes(s.name))
                    : allSeries.slice(0, 3)
            );

            $('#filterModal').modal('hide');
        });

        $(document).on('click', '[data-bs-toggle="refresh"]', function() {
            $('.model-checkbox').prop('checked', false);
            $('.model-checkbox').slice(0, 3).prop('checked', true);
            chart.updateSeries(allSeries.slice(0, 3));

            initDefaultDate();

            $('#filterModal').modal('hide');
        });
    </script> -->

    <script>
        let chart = null;
        let allModels = [];

        const statusMap = {
            "not target": 1,
            "target": 2,
            "not running": 0
        };

        const reverseStatusMap = {
            0: "Not Running",
            1: "Not Target",
            2: "Target"
        };

        function getDefaultDateRange() {

            const today = new Date();

            const lastWeek = new Date();
            lastWeek.setDate(today.getDate() - 6);

            return {
                fromDate: lastWeek.toISOString().split('T')[0],
                toDate: today.toISOString().split('T')[0]
            };
        }

        function loadModels() {

            $.ajax({
                url: '/connectify-web/controllers/DailyTargetReportController.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    type: 'get-models'
                },
                success: function(res) {

                    if (!res.success) return;

                    allModels = res.data;

                    initModelFilter();
                }
            });
        }

        function initModelFilter() {

            const container = $('#modelDropdown');

            container.empty();

            allModels.forEach((model, idx) => {

                container.append(`
            <div class="form-check">
                <input
                    class="form-check-input model-checkbox"
                    type="checkbox"
                    value="${model}"
                    id="model_${idx}"
                >
                <label
                    class="form-check-label"
                    for="model_${idx}">
                    ${model}
                </label>
            </div>
        `);

            });

            $('.model-checkbox')
                .slice(0, 3)
                .prop('checked', true);
        }

        function loadChart(fromDate, toDate, selectedModels = []) {

            $.ajax({
                url: '/connectify-web/controllers/DailyTargetReportController.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    type: 'target-report-chart',
                    fromDate: fromDate,
                    toDate: toDate
                },
                success: function(res) {

                    if (!res.success) return;

                    const rawData = res.data;

                    const categories = [...new Set(
                        rawData.map(item => item.date)
                    )].sort();

                    const modelsToShow =
                        selectedModels.length ?
                        selectedModels :
                        allModels.slice(0, 3);

                    const series = modelsToShow.map(model => {

                        const data = categories.map(date => {

                            const found = rawData.find(item =>
                                item.model_name === model &&
                                item.date === date
                            );

                            return found ?
                                statusMap[
                                    found.uph_status_name.toLowerCase()
                                ] :
                                0;
                        });

                        return {
                            name: model,
                            data: data
                        };
                    });

                    const options = {

                        series: series,

                        chart: {
                            type: 'area',
                            height: 400,
                            toolbar: {
                                show: true
                            }
                        },

                        stroke: {
                            width: 3,
                            curve: 'smooth'
                        },

                        markers: {
                            size: 5
                        },

                        xaxis: {
                            categories: categories,
                            title: {
                                text: ''
                            }
                        },

                        yaxis: {
                            min: 0,
                            max: 2,
                            tickAmount: 2,
                            title: {
                                text: '......'
                            },
                            labels: {
                                formatter: function(val) {
                                    return reverseStatusMap[val] || val;
                                },
                            }
                        },

                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return reverseStatusMap[val] || '-';
                                }
                            }
                        }
                    };

                    if (!chart) {

                        chart = new ApexCharts(
                            document.querySelector("#targetReportsChart"),
                            options
                        );

                        chart.render();

                    } else {

                        chart.updateOptions({
                            xaxis: {
                                categories: categories
                            }
                        });

                        chart.updateSeries(series);
                    }
                }
            });
        }

        $(document).ready(function() {

            const range = getDefaultDateRange();

            $('#fromDate').val(range.fromDate);
            $('#toDate').val(range.toDate);

            loadModels();

            setTimeout(() => {

                loadChart(
                    range.fromDate,
                    range.toDate
                );

            }, 300);

        });

        $('#applyFilter').on('click', function() {

            const selectedModels = $('.model-checkbox:checked')
                .map(function() {
                    return this.value;
                })
                .get();

            loadChart(
                $('#fromDate').val(),
                $('#toDate').val(),
                selectedModels
            );

            $('#filterModal').modal('hide');
        });

        $(document).on('click', '[data-bs-toggle="refresh"]', function() {

            const range = getDefaultDateRange();

            $('#fromDate').val(range.fromDate);
            $('#toDate').val(range.toDate);

            $('.model-checkbox').prop('checked', false);

            $('.model-checkbox')
                .slice(0, 3)
                .prop('checked', true);

            loadChart(
                range.fromDate,
                range.toDate
            );
        });

        // function updateSelectedRange() {
        //     const from = $('#fromDate').val();
        //     const to = $('#toDate').val();

        //     $('#selectedRange').html(
        //         `<strong>Date:</strong> ${from} ↣ ${to}`
        //     );
        // }
        // $('#fromDate, #toDate').on('change', updateSelectedRange);

        $('#fromDate, #toDate').on('change', function() {
            const from = new Date($('#fromDate').val());
            const to = new Date($('#toDate').val());

            if (to < from) {
                alert("To Date can't be earlier than the From Date");
                $('#toDate').val($('#fromDate').val());
            }
        });
    </script>

    <script>
        $.ajax({
            url: '/connectify-web/controllers/ReportController.php?type=total-by-model',
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
    </script>

</body>

</html>