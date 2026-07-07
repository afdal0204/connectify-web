<?php
include './../../config.php';

session_start();

$deptRes = $conn->query("SELECT id, department_name, remark FROM department ORDER BY department_name ASC");
$modelRes = $conn->query("SELECT id, model_name FROM models ORDER BY model_name ASC");
$modelResModal = $conn->query("SELECT id, model_name FROM models ORDER BY model_name ASC");
$errorRes = $conn->query("SELECT id, error_code, symptom FROM error_code ORDER BY error_code ASC");
$stationRes = $conn->query("SELECT id, station_name FROM stations ORDER BY station_name ASC");
$deviceRes  = $conn->query("SELECT id, device_name FROM devices ORDER BY device_name ASC");

$role_id = $_SESSION['role_id'] ?? 'Guest';
$department_id = $_SESSION['department_id'];
$deptRemark = $_SESSION['deptRemark'];
date_default_timezone_set('Asia/Jakarta');
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
    <title>Connectify | SQE Report</title>

    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">

    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">
    <link rel="stylesheet" href="/connectify-web/assets/css/flatpickr.min.css">
    <script src="/connectify-web/assets/js/flatpickr.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    <style>
        #reportTable td,
        #reportTable th {
            white-space: normal !important;
        }

        .remark-text,
        .action-taken-text,
        .root-cause-text {
            white-space: pre-line;
        }
    </style>
</head>

<body>
    <?php
    require_once '../layout/header.php';
    require_once '../layout/sidebar.php';
    ?>
    <main class="nxl-container">
        <div class="nxl-content">
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/connectify-web/pages/dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">SQE Reports</li>
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
                        <div class="d-flex align-items-center gap-2 page-header-right-items-wrapper">
                            <div class="dropdown filter-dropdown">
                                <a class="btn btn-md btn-light-brand" data-bs-toggle="modal" data-bs-target="#abnormalFilterModal" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                    <i class="feather-filter me-2"></i>
                                    <span>Filter</span>
                                </a>
                            </div>
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createReportModal">
                                <i class="feather-plus me-2"></i>
                                <span>Add Report</span>
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
            <div class="main-content">
                <div class="row g-3 px-0 mb-2 align-items-end">
                    <div class="col-md-12 d-flex align-items-end justify-content-end">
                        <input type="search" id="customSearchBox" class="form-control" placeholder="Search..." style="max-width: 250px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">SQE Reports</h5>

                                <div class="card-header-action">
                                    <div id="exportButtonsContainer"></div>
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a id="btnClearFilter1" href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-2 m-2">
                                <div class="table-responsive">
                                    <div id="alertReportContainer"></div>
                                    <table id="reportTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>No</th>
                                                <th>Department Code</th>
                                                <th>Status(Open/Close)</th>
                                                <th>Issue Date</th>
                                                <th>Item</th>
                                                <th>Highlight From</th>
                                                <th>Customer</th>
                                                <th>Line Area</th>
                                                <th>Model</th>
                                                <th>P/N</th>
                                                <th>Supplier</th>
                                                <th>Issue</th>
                                                <th>Issue Description</th>
                                                <th>Responsible</th>
                                                <th>F/R</th>
                                                <th>Stock</th>
                                                <th>Immediately action</th>
                                                <th>Sorting/Rework</th>
                                                <th>Root Cause</th>
                                                <th>Short term solution</th>
                                                <th>Long term solution</th>
                                                <th>8D report received day</th>
                                                <th>Action Lot</th>
                                                <th>SQE Owner(Created By)</th>
                                                <th>Remark/ E-Car/ QIT No.</th>
                                                <th>BTC No.</th>
                                                <th>Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportTableBody">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        require_once '../layout/footer.php';
        ?>
    </main>

    <!-- Modal Filter -->
    <div class="modal fade" id="abnormalFilterModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Filter</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body pt-3">
                    <div class="container-fluid">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Model</label>
                                <select id="filterModel" class="form-select">
                                    <option value="">All</option>
                                    <?php $modelRes->data_seek(0);
                                    while ($row = $modelRes->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['model_name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Station</label>
                                <select id="filterStation" class="form-select">
                                    <option value="">All</option>
                                    <?php $stationRes->data_seek(0);
                                    while ($row = $stationRes->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['station_name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Device</label>
                                <select id="filterDevice" class="form-select">
                                    <option value="">All</option>
                                    <?php $deviceRes->data_seek(0);
                                    while ($row = $deviceRes->fetch_assoc()): ?>
                                        <option value="<?= $row['id'] ?>">
                                            <?= $row['device_name'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date"
                                    id="filterDateFrom"
                                    class="form-control"
                                    max="<?= date('Y-m-d') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date"
                                    id="filterDateTo"
                                    class="form-control"
                                    max="<?= date('Y-m-d') ?>">
                            </div>

                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button id="btnClearFilter" class="btn btn-light" data-bs-dismiss="modal">Clear</button>
                    <button class="btn btn-success" id="btnApplyFilter">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Report Modal -->
    <div class="modal fade" id="createReportModal" tabindex="-1" aria-labelledby="createReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createReportModalLabel">Create New Report</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="reportForm" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Status<small class="text-muted">(Open/Close)</small></label>
                            <select id="status" class="form-select" required>
                                <option value="">-----</option>
                                <option value="Open">Open</option>
                                <option value="Close">Close</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Date</label>
                            <input type="date" id="date" class="form-control" required
                                max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Item</label>
                            <input type="text" id="item" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Highlight From</label>
                            <input type="text" id="highlightFrom" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Customer</label>
                            <input type="text" id="customer" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Model</label>
                            <select id="modelSelect" class="form-select" required>
                                <option value="">----</option>
                                <?php while ($row = $modelResModal->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['model_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">P/N (Product Number)</label>
                            <input type="text" id="productNumber" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <input type="text" id="supplier" class="form-control">
                        </div>
                        <div class="col-md-5">
                            <label class="form-label">Issue</label>
                            <textarea id="issue" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Issue Description</label>
                            <textarea id="issueDescription" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Responsible</label>
                            <input type="text" id="responsiblePerson" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Failure Rate (F/R)</label>
                            <input type="text" id="failureRate" class="form-control" min="0" max="100" step="0.01">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Stock</label>
                            <input type="text" id="stock" class="form-control">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Immediately Action</label>
                            <textarea id="immediatelyAction" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sorting/Rework</label>
                            <textarea id="sortingRework" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Root Cause Analysis</label>
                            <textarea id="rootCause" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Short Term Solution</label>
                            <textarea id="shortTermSolution" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Long Term Solution</label>
                            <textarea id="longTermSolution" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">8D Report Received Day</label>
                            <input type="date" id="reportReceivedDay" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Action Lot</label>
                            <input type="text" id="actionLot" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">BTC No.</label>
                            <input type="text" id="btcNo" class="form-control">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Remark/ E-Car/ QIT No.</label>
                            <textarea id="remark" class="form-control" rows="2"></textarea>
                        </div>

                        <input type="hidden" id="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
                    </form>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer col-12 d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-secondary" id="clear">Clear</button>
                    <button type="button" class="btn btn-success" id="save">Save</button>
                </div>

            </div>
        </div>
    </div>

    <script src="/connectify-web/assets/vendors/js/jquery.time-to.min.js "></script>
    <script src="/connectify-web/assets/vendors/js/vendors.min.js"></script>
    <!-- vendors.min.js {always must need to be top} -->
    <script src="/connectify-web/assets/vendors/js/dataTables.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/dataTables.bs5.min.js"></script>
    <script src="/connectify-web/assets/js/leads-init.min.js"></script>

    <script src="/connectify-web/assets/vendors/js/apexcharts.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/select2.min.js"></script>
    <!-- <script src="/connectify-web/assets/vendors/js/select2-active.min.js"></script> -->
    <script src="/connectify-web/assets/js/common-init.min.js"></script>
    <script src="assets/js/projects-init.min.js"></script>
    <script src="/connectify-web/assets/js/widgets-tables-init.min.js"></script>
    <script src="/connectify-web/assets/js/theme-customizer-init.min.js"></script>

    <script src="/connectify-web/assets/bootstrap-5/DataTables/dataTables.buttons.min.js"></script>
    <script src="/connectify-web/assets/bootstrap-5/DataTables/jszip.min.js"></script>
    <script src="/connectify-web/assets/bootstrap-5/DataTables/buttons.html5.min.js"></script>

    <script src="/connectify-web/pages/js/dashboard.js"></script>
    <script src="../js/index.js"></script>
    <script>
        const LOGGED_USER_ID = <?= json_encode($_SESSION['user_id'] ?? null) ?>;
        const LOGGED_USER_ROLE = <?= json_encode($_SESSION['role_id'] ?? null) ?>;
    </script>
    <script>
        setInterval(() => {
            fetch("/connectify-web/update_activity.php");
        }, 60000);
    </script>
        <script>

    </script>
    <script>
        const today = new Date();
        const fromPicker = flatpickr("#date", {
            dateFormat: "Y-m-d",
            maxDate: today,
            onChange: function(selectedDates, dateStr, instance) {
                toPicker.set("minDate", dateStr);
            }
        });
        const fromPicker3 = flatpickr("#reportReceivedDay", {
            dateFormat: "Y-m-d",
            maxDate: today,
            onChange: function(selectedDates, dateStr, instance) {
                toPicker.set("minDate", dateStr);
            }
        });

        const fromPicker1 = flatpickr("#filterDateFrom", {
            dateFormat: "Y-m-d",
            maxDate: today,
            onChange: function(selectedDates, dateStr, instance) {
                toPicker.set("minDate", dateStr);
            }
        });

        const toPicker = flatpickr("#filterDateTo", {
            dateFormat: "Y-m-d",
            maxDate: today,
            onChange: function(selectedDates, dateStr, instance) {
                fromPicker1.set("maxDate", dateStr);
            }
        });
    </script>
    <script>
        function showSuccessToast(message) {
            Swal.fire({
                title: "Success!",
                text: message,
                icon: "success",
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "btn btn-success"
                },
                buttonsStyling: false
            });
        }

        function showErrorToast(message) {
            Swal.fire({
                title: "Failed!",
                text: message,
                icon: "error",
                confirmButtonText: "OK",
                customClass: {
                    confirmButton: "btn btn-danger"
                },
                buttonsStyling: false
            });
        }

        $(document).ready(function() {
            const reportTable = $('#reportTable').DataTable({
                // dom: 'Bfrtip',
                // dom: 'Brtip',
                dom: 'Blrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="feather-download me-2"></i> Generate Report',
                    title: 'SQE Report',
                    className: 'btn btn-xs btn-primary rounded',

                    exportOptions: {
                        // columns: ':visible',
                        columns: ':not(.no-export)',
                        modifier: {
                            search: 'applied',
                            order: 'applied',
                            page: 'all'
                        }
                    },

                    // export to excel
                    customize: function(xlsx) {
                        const sheet = xlsx.xl.worksheets['sheet1.xml'];
                        const styles = xlsx.xl['styles.xml'];

                        // Add a bold+large font for the title
                        const fonts = $('fonts', styles);
                        const titleFontIndex = fonts.children().length;
                        fonts.append(`
                            <font>
                                <b/>
                                <sz val="16"/>
                                <color auto="1"/>
                            </font>
                        `);

                        // Add border
                        const borders = $('borders', styles);
                        const borderIndex = borders.children().length;
                        borders.append(`
                            <border>
                                <left style="thin"><color auto="1"/></left>
                                <right style="thin"><color auto="1"/></right>
                                <top style="thin"><color auto="1"/></top>
                                <bottom style="thin"><color auto="1"/></bottom>
                            </border>
                        `);

                        // Add styles
                        const cellXfs = $('cellXfs', styles);

                        // Title style: large bold font, no border, center
                        cellXfs.append(`
                            <xf xfId="0" fontId="${titleFontIndex}" applyFont="1" applyAlignment="1">
                                <alignment horizontal="center" vertical="center"/>
                            </xf>
                        `);
                        const titleStyleIndex = cellXfs.children().length - 1;

                        // Body style: border, center, wrap
                        cellXfs.append(`
                            <xf xfId="0" borderId="${borderIndex}" applyBorder="1" applyAlignment="1">
                                <alignment horizontal="center" vertical="center" wrapText="1"/>
                            </xf>
                        `);

                        // Header style: bold font, border, center, wrap
                        cellXfs.append(`
                            <xf xfId="0" fontId="1" borderId="${borderIndex}" applyFont="1" applyBorder="1" applyAlignment="1">
                                <alignment horizontal="center" vertical="center" wrapText="1"/>
                            </xf>
                        `);
                        const bodyStyleIndex = cellXfs.children().length - 2;
                        const headerStyleIndex = cellXfs.children().length - 1;

                        // Get total column count
                        const totalCols = $('row:eq(1) c', sheet).length;
                        const lastColLetter = String.fromCharCode(64 + totalCols); // e.g. Z=90-64=26

                        // Insert title row at top
                        const sheetData = $('sheetData', sheet);
                        sheetData.prepend(`
                            <row r="1">
                                <c t="inlineStr" r="A1" s="${titleStyleIndex}">
                                    <is>
                                        <t>SQE Report</t>
                                    </is>
                                </c>
                            </row>
                        `);

                        // Merge title cells A1:{lastCol}1
                        const mergeTags = $('mergeCells', sheet);
                        if (mergeTags.length === 0) {
                            sheet.children().last().after(`<mergeCells count="1"><mergeCell ref="A1:${lastColLetter}1"/></mergeCells>`);
                        } else {
                            mergeTags.append(`<mergeCell ref="A1:${lastColLetter}1"/>`);
                            mergeTags.attr('count', mergeTags.children().length);
                        }

                        // Apply body style to all data rows, header style to row 2 (header after title row)
                        $('row c', sheet).attr('s', bodyStyleIndex);
                        $('row:eq(1) c', sheet).attr('s', headerStyleIndex);
                        // Keep title style on A1
                        $('row:eq(0) c[r="A1"]', sheet).attr('s', titleStyleIndex);
                    }
                }],

                ajax: {
                    url: '/connectify-web/controllers/MultiDeptReportController.php?type=SQE-report',
                    type: 'GET',
                    data: function(d) {
                        d.filter_dept = $('#filterDept').val();
                        d.filter_model = $('#filterModel').val();
                        d.filter_station = $('#filterStation').val();
                        d.filter_device = $('#filterDevice').val();
                        d.filter_date_from = $('#filterDateFrom').val();
                        d.filter_date_to = $('#filterDateTo').val();
                    },
                    dataSrc: function(json) {
                        return json.success ? json.data : [];
                    }
                },
                columns: [
                    {
                        data: null,
                        render: (data, type, row, meta) => {
                            const num = meta.row + 1;
                            if (type === 'export') return num + '.';
                            return num;
                        }
                    },
                    {
                        data: 'department_name',
                        render: function(data, type, row) {
                            if (type === 'export') {
                                return row.dept_remark ? data + ' (' + row.dept_remark + ')' : data;
                            }
                            return row.dept_remark
                                ? `${data} (${row.dept_remark})`
                                : data;
                        }
                    },
                    {
                        data: 'status',
                        className: 'text-center',
                        render: function(data, type, row) {
                            if (type === 'export') {
                                return data || '';
                            }
                            if (data === 'Close') {
                                return `<span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Close</span>`;
                            }
                            return `
                                <select class="form-select form-select-sm status-select" data-id="${row.id}" style="width:120px;">
                                    <option value="Open" selected>🟠 Open</option>
                                    <option value="Close">🟢 Close</option>
                                </select>
                            `;
                        }
                    },
                    { data: 'date' },
                    { data: 'item' },
                    { data: 'highlight_from' },
                    { data: 'customer' },
                    { data: 'line_area' },
                    { data: 'model_name' },
                    { data: 'product_number' },
                    { data: 'supplier' },
                    {
                        data: 'issue',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="root-cause-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    {
                        data: 'issue_description',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="action-taken-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    { data: 'responsible_person' },
                    { data: 'failure_rate' },
                    { data: 'stock' },
                    {
                        data: 'immediately_action',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="action-taken-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    {
                        data: 'sorting_rework',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return data;
                        }
                    },
                    {
                        data: 'root_cause',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="root-cause-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    {
                        data: 'short_term_solution',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="action-taken-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    {
                        data: 'long_term_solution',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="remark-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    { data: '8d_report_received_day' },
                    { data: 'action_lot' },
                    { data: 'sqe_owner' },
                    {
                        data: 'remark',
                        render: function(data, type) {
                            if (type === 'export') return data || '';
                            if (!data) return '';
                            return `<div class="remark-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    { data: 'btc_no' },
                    {
                        data: null,
                        className: 'no-export',
                        orderable: false,
                        render: function(data, type, row) {
                            const isAdmin = [1, 2].includes(parseInt(LOGGED_USER_ROLE));
                            const isOwner = parseInt(row.user_id) === parseInt(LOGGED_USER_ID);
                            if (!isAdmin && !isOwner) return '';
                            return `
                                <div class="d-flex justify-content-center">
                                    <a href="#" class="btn btn-sm btn-danger btn-delete-report" data-id="${row.id}">
                                        <i class="feather-trash"></i>
                                    </a>
                                </div>
                            `;
                        }
                    }
                ],
                columnDefs: [
                    { targets: -1, orderable: false }
                ],
                responsive: true,
                pageLength: 5,
                lengthMenu: [5, 10, 25, 50, 100],
                language: {
                    paginate: {
                        previous: 'Prev',
                        next: 'Next'
                    },
                    lengthMenu: "Show _MENU_ entries per page",
                    zeroRecords: "No records found",
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                    infoEmpty: "No data available",
                    infoFiltered: "(filtered from _MAX_ total entries)"
                }
            });

            reportTable.buttons().container().appendTo('#exportButtonsContainer');
            $('#customSearchBox').on('keyup', function() {
                reportTable.search(this.value).draw();
            });

            $('#btnApplyFilter').click(function() {
                reportTable.ajax.reload();
                $('#abnormalFilterModal').modal('hide');
            });

            $('#btnClearFilter').click(function() {
                $('#filterDept').val('');
                $('#filterModel').val('');
                $('#filterStation').val('');
                $('#filterDevice').val('');
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                reportTable.ajax.reload();
            });
            $('#btnClearFilter1').click(function() {
                $('#filterDept').val('');
                $('#filterModel').val('');
                $('#filterStation').val('');
                $('#filterDevice').val('');
                $('#filterDateFrom').val('');
                $('#filterDateTo').val('');
                reportTable.ajax.reload();
            });

            // edit report
            $(document).on('change', '.status-select', function () {

                const id = $(this).data('id');
                const status = $(this).val();

                $.ajax({
                    url: '/connectify-web/controllers/MultiDeptReportController.php',
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        type: 'update-status',
                        id: id,
                        status: status
                    }),
                    success: function(res) {
                        if(res.success){
                            showSuccessToast("Status updated");
                        }else{
                            showErrorToast(res.message);
                        }
                    },
                    error:function(){
                        showErrorToast("Failed to update status");
                    }
                });

            });

            // Delete report
            $(document).on('click', '.btn-delete-report', function(e) {
                e.preventDefault();

                const reportId = $(this).data('id');

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "Are you sure?",
                    text: "You want to delete this report!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed || result.value === true) {
                        $.ajax({
                            url: '/connectify-web/controllers/MultiDeptReportController.php',
                            type: 'DELETE',
                            data: JSON.stringify({
                                id: reportId
                            }),
                            contentType: 'application/json',
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    swalWithBootstrapButtons.fire(
                                        "Deleted!",
                                        response.message,
                                        "success"
                                    );

                                    $('#reportTable')
                                        .DataTable()
                                        .ajax.reload(null, false);

                                } else {

                                    swalWithBootstrapButtons.fire(
                                        "Failed!",
                                        response.message,
                                        "error"
                                    );
                                }
                            },
                            error: function(xhr) {
                                swalWithBootstrapButtons.fire(
                                    "Error!",
                                    "Something went wrong!",
                                    "error"
                                );
                            }
                        });

                    } else if (result.dismiss === Swal.DismissReason.cancel) {

                        swalWithBootstrapButtons.fire(
                            "Cancelled",
                            "Your data is safe :)",
                            "error"
                        );
                    }
                });
            });

            function showAlert(title, message, type) {
                const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                <strong>${title}:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
                $('#alertReportContainer').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
        });
    </script>

    <!-- filter data -->
    <script>
        $(document).ready(function() {

            $('#filterDept').on('change', function() {
                const dept_id = $(this).val();

                $('#filterModel').prop('disabled', true).html('<option value="">Loading...</option>');
                $('#filterStation').prop('disabled', true).html('<option value="">All</option>');
                $('#filterDevice').prop('disabled', true).html('<option value="">All</option>');

                $.ajax({
                    url: '/connectify-web/pages/reports/get-data.php',
                    type: 'POST',
                    data: {
                        action: 'getModels',
                        dept_id: dept_id
                    },
                    dataType: 'json',
                    success: function(data) {

                        $('#filterModel')
                            .prop('disabled', false)
                            .html('<option value="">All</option>');

                        data.forEach(obj => {
                            $('#filterModel').append(
                                `<option value="${obj.id}">${obj.model_name}</option>`
                            );
                        });
                    }
                });
            });

            $('#filterModel').on('change', function() {
                const model_id = $(this).val();

                $('#filterStation')
                    .prop('disabled', true)
                    .html('<option value="">Loading...</option>');

                $('#filterDevice')
                    .prop('disabled', true)
                    .html('<option value="">All</option>');

                $.ajax({
                    url: '/connectify-web/pages/reports/get-data.php',
                    type: 'POST',
                    data: {
                        action: 'getStations',
                        model_id: model_id
                    },
                    dataType: 'json',
                    success: function(data) {

                        $('#filterStation')
                            .prop('disabled', false)
                            .html('<option value="">All</option>');

                        data.forEach(obj => {
                            $('#filterStation').append(
                                `<option value="${obj.id}">${obj.station_name}</option>`
                            );
                        });
                    }
                });
            });

            $('#filterStation').on('change', function() {
                const station_id = $(this).val();

                $('#filterDevice')
                    .prop('disabled', true)
                    .html('<option value="">Loading...</option>');

                $.ajax({
                    url: '/connectify-web/pages/reports/get-data.php',
                    type: 'POST',
                    data: {
                        action: 'getDevices',
                        station_id: station_id
                    },
                    dataType: 'json',
                    success: function(data) {

                        $('#filterDevice')
                            .prop('disabled', false)
                            .html('<option value="">All</option>');

                        data.forEach(obj => {
                            $('#filterDevice').append(
                                `<option value="${obj.id}">${obj.device_name}</option>`
                            );
                        });
                    }
                });
            });

        });
    </script>

    <!-- add report -->
    <script>
        document.getElementById("date").addEventListener("input", function () {
            let today = new Date().toISOString().split("T")[0];

            if (this.value > today) {
                alert("Tanggal tidak boleh lebih dari hari ini!");
                this.value = today;
            }
        });

        $('#save').click(function (e) {
            e.preventDefault();

            let formData = new FormData();

            formData.append('type', 'SQE-report');
            formData.append('model_id', $('#modelSelect').val());
            formData.append('date', $('#date').val());
            formData.append('failure_rate', $('#failureRate').val());
            formData.append('item', $('#item').val());

            // SQE-specific fields
            formData.append('highlight_from', $('#highlightFrom').val());
            formData.append('customer', $('#customer').val());
            formData.append('product_number', $('#productNumber').val());
            formData.append('supplier', $('#supplier').val());
            formData.append('issue', $('#issue').val());
            formData.append('issue_description', $('#issueDescription').val());
            formData.append('stock', $('#stock').val());
            formData.append('immediately_action', $('#immediatelyAction').val());
            formData.append('sorting_rework', $('#sortingRework').val());
            formData.append('8d_report_received_day', $('#reportReceivedDay').val());
            formData.append('action_lot', $('#actionLot').val());
            formData.append('btc_no', $('#btcNo').val());

            formData.append('root_cause', $('#rootCause').val());
            formData.append('short_term_solution', $('#shortTermSolution').val());
            formData.append('long_term_solution', $('#longTermSolution').val());

            formData.append('responsible_person', $('#responsiblePerson').val());
            formData.append('status', $('#status').val());

            formData.append('remark', $('#remark').val());
            formData.append('user_id', $('#user_id').val());

            // Debug: log all form data being sent
            console.log('=== SQE Report Form Data ===');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }

            $.ajax({
                url: '/connectify-web/controllers/MultiDeptReportController.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',

                success: function (response) {
                    // console.log('=== Server Response ===', response);
                    if (response.success) {
                        $('#createReportModal').modal('hide');
                        showSuccessToast(response.message);
                        $('#reportForm')[0].reset();
                        $('#reportTable').DataTable().ajax.reload(null, false);
                    } else {
                        showErrorToast(response.message || 'Failed to add report');
                    }
                },

                error: function (xhr) {
                    // console.log('=== Server Error ===', xhr.status, xhr.responseText);
                    let msg = "Unexpected error";

                    try {
                        let res = JSON.parse(xhr.responseText);
                        if (res.message) msg = res.message;
                    } catch {}

                    showErrorToast(msg);
                }
            });
        });

        $('#clear').click(function() {
            $('#reportForm')[0].reset();
            $('#createReportModal').modal('hide');
        });
    </script>
</body>

</html>