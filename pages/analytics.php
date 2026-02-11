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
                    </div>
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Total reports by Users</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a id="btnRefresh" href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-0">
                                <div id="topReporter"></div>
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

    <!-- Modal Filter -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- <div class="row mb-3 p-4">
                    <div class="col-6">
                        <label class="form-label fw-semibold">From Date</label>
                        <input type="date" id="fromDate" class="form-control form-control-sm"
                            max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold">To Date</label>
                        <input type="date" id="toDate" class="form-control form-control-sm"
                            max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                    </div>
                </div> -->
                <div class="modal-body">
                    <label class="form-label fw-semibold">Select Model</label>

                    <div id="modelDropdown"
                        class="border rounded p-2"
                        style="max-height: 280px; overflow-y: auto;">
                        <!-- checkbox via JS -->
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

                allSeries = allModels.map(model => {
                    const data = isoDates.map((iso, idx) => {
                        const date = categories[idx];
                        const found = rawData.find(item =>
                            item.model_name === model &&
                            item.date === date
                        );
                        return found ? statusMap[found.uph_status_name.toLowerCase()] : 0;
                    });

                    return {
                        name: model,
                        data
                    };
                });

                // 🔹 DEFAULT: hanya 3 garis pertama
                const defaultSeries = allSeries.slice(0, 3);

                var options = {
                    series: defaultSeries,
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
        $('#applyFilter').on('click', function() {
            const selected = $('.model-checkbox:checked')
                .map(function() {
                    return this.value;
                }).get();

            chart.updateSeries(
                selected.length ?
                allSeries.filter(s => selected.includes(s.name)) :
                allSeries.slice(0, 3)
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
                            type: 'area',
                            height: 350,
                            toolbar: {
                                show: true
                            }
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
                            min: 0
                        },
                        markers: {
                            size: 5
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        title: {
                            // text: 'Tota Report per Model',
                            align: 'center'
                        },
                        tooltip: {
                            enabled: true
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
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        xaxis: {
                            categories: departmentNames,
                            title: {
                                text: 'Department'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Total Reports'
                            },
                            min: 0
                        },
                        plotOptions: {
                            bar: {
                                horizontal: false,
                                columnWidth: '70%',
                                endingShape: 'rounded'
                            }
                        },
                        dataLabels: {
                            enabled: true,
                            // formatter: function (val) {
                            //     return parseInt(val); 
                            // }
                        },
                        title: {
                            // text: 'Total Reports per Department',
                            align: 'center'
                        },
                        tooltip: {
                            enabled: true,
                            y: {
                                formatter: function (val) {
                                    return parseInt(val); 
                                }
                            }
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

        $.ajax({
            url: '/connectify-web/controllers/ReportController.php?type=top-reporter',
            type: 'GET',
            dataType: 'json',
            success: function(json) {
                if (json.success && Array.isArray(json.data)) {
                    const labels = json.data.map(item => item.name);
                    const totalReports = json.data.map(item => parseInt(item.total_report));

                    var options = {
                        series: [{
                            name: 'Total Reports',
                            data: totalReports
                        }],
                        chart: {
                            type: 'area',
                            height: 350,
                            toolbar: {
                                show: true
                            }
                        },
                        xaxis: {
                            categories: labels,
                            title: {
                                text: 'Name'
                            }
                        },
                        yaxis: {
                            title: {
                                text: 'Total Reports'
                            },
                            min: 0
                        },
                        markers: {
                            size: 5
                        },
                        stroke: {
                            curve: 'smooth'
                        },
                        title: {
                            // text: 'Tota Report per Model',
                            align: 'center'
                        },
                        tooltip: {
                            enabled: true
                        }
                    };

                    var chart = new ApexCharts(document.querySelector("#topReporter"), options);
                    chart.render();
                }
            },
            error: function(xhr, status, error) {
                console.error("Chart data fetch error:", error);
            }
        });
    </script>

</body>

</html>