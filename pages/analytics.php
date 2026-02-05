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
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">Analytics</li>
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
                <div class="row">
                    <!-- Chart -->

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
                                <div id="dailyReportsChart"></div>
                                <div id="chartTooltip"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xxl-6">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Abnormal Report</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div id="abnormalReportsChart"></div>
                                <!-- <canvas id="abnormalReportsChart"></canvas> -->
                            </div>
                        </div>
                    </div>

                    <div class="col-xxl-6">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Line Report Per Shift</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div id="lineReportChart"></div>
                                <!-- <canvas id="targetReportsChart"></canvas> -->
                                <div id="chartTooltip"></div>
                            </div>
                        </div>
                    </div>
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
        $.ajax({
            url: '/connectify-web/controllers/DailyTargetReportController.php?type=target-report-chart',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if (!res.success) return;

                const rawData = res.data;

                // 1. Ambil dan ubah ke ISO
                const categories = [...new Set(rawData.map(item => item.date))].sort();
                const isoDates = categories.map(d => new Date(d).toISOString());

                // 2. Ambil daftar model unik
                const models = [...new Set(rawData.map(item => item.model_name))].sort();

                // 3. Mapping status
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

                // 4. Build series
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

                // 5. Render chart
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
                        title: {
                            text: '......'
                        },
                        tickAmount: 2,
                        labels: {
                            formatter: val => reverseStatusMap[val] || val
                        },
                    },
                    tooltip: {
                        y: {
                            formatter: val => reverseStatusMap[val] || '-'
                        }
                    }
                };

                var chart = new ApexCharts(document.querySelector("#dailyReportsChart"), options);
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

                    var options = {
                        series: [{
                            name: 'Total Reports',
                            data: totalReports
                        }],
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: labels,
                            title: {
                                text: 'Model Name'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Total Reports'
                            },
                            min: 0,
                            tickAmount: 5,
                            labels: {
                                formatter: function(value) {
                                    return value.toFixed(0);
                                }
                            }
                        },
                        fill: {
                            colors: ['#14a043']
                        },
                        stroke: {
                            width: 1,
                            colors: ['rgb(18, 100, 133)']
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value.toFixed(0);
                                }
                            }
                        },
                        grid: {
                            show: true,
                            borderColor: '#e7e7e7',
                            strokeDashArray: 3
                        },
                        legend: {
                            show: false
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#abnormalReportsChart"), options);
                    chart.render();
                }
            },
            error: function(xhr, status, error) {
                console.error("Chart data fetch error:", error);
            }
        });

        $.ajax({
            url: '/connectify-web/controllers/LineReportController.php?type=total-by-model',
            type: 'GET',
            data: {
                departmentIds: [1, 2, 3]
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const totals = response.data;

                    let departmentNames = [];
                    let totalReports = [];

                    for (let key in totals) {
                        departmentNames.push(totals[key].department_name);
                        totalReports.push(totals[key].total_reports);
                    }
                    var options = {
                        series: [{
                            name: 'Total Reports',
                            data: totalReports
                        }],
                        chart: {
                            type: 'bar',
                            height: 350
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: false
                        },
                        xaxis: {
                            categories: departmentNames,
                            title: {
                                text: 'Departments'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Total Reports'
                            }
                        },
                        tooltip: {
                            y: {
                                formatter: function(value) {
                                    return value.toFixed(0);
                                }
                            }
                        },
                        fill: {
                            colors: ['#14a043']
                        },
                        stroke: {
                            width: 1,
                            colors: ['rgb(38, 100, 136)']
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#lineReportChart"), options);
                    chart.render();
                } else {
                    console.error("Failed to load data:", response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching data:", error);
            }
        });
    </script>

</body>

</html>