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
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Daily Target Report</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div id="lineReportsChart"></div>
                                <div id="chartTooltip"></div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="col-xxl-3 col-md-6">
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
                                    <a href="javascript:void(0);" class="">
                                        <i class="feather-more-vertical"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <!-- Chart -->
                    <!-- <div class="col-xxl-6">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Abnormal Report Chart</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div id="payment-records-chart"></div>
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
                                <div id="daily-target-chart"></div>
                                <canvas id="targetReportsChart"></canvas>
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
        $(window).on('load', function() {
            $('#preloader').fadeOut('slow', function() {
                $(this).remove();
            });

            getTotalUsers();
            getTotalReports();
            getTotalLineReports()
            getTotalTargetReports();
            getTotalErrorCode();
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
    </script>

    <script>
        $.ajax({
            url: '/connectify-web/controllers/DailyTargetReportController.php?type=target-report-chart',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.success) return;

                const rawData = res.data;

                const categories = [...new Set(rawData.map(item => item.date))].sort();
                const isoDates = categories.map(d => new Date(d).toISOString());

                const models = [...new Set(rawData.map(item => item.model_name))].sort();

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

                const series = models.map(model => {
                    const data = isoDates.map((iso, idx) => {
                        const date = categories[idx];
                        const found = rawData.find(item =>
                            item.model_name === model &&
                            item.date === date
                        );
                        const val = found ? statusMap[found.uph_status_name.toLowerCase()] : 0;
                        console.log(`Model ${model}, date ${date} →`, val);
                        return val;
                    });
                    return {
                        name: model,
                        data
                    };
                });

                console.log("Series final:", series);

                var options = {
                    series,
                    chart: {
                        type: 'area',
                        height: 350,
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

                var chart = new ApexCharts(document.querySelector("#lineReportsChart"), options);
                chart.render();
            },
            error: function(err) {
                console.error(err);
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

        $.ajax({
            url: '/connectify-web/controllers/DailyTargetReportController.php?type=target-report-chart',
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