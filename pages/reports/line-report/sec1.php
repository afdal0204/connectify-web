<?php
include '../../../config.php';
// include '../init.php';
session_start();
// include '../auth_check.php';

// $modelResModal = $conn->query("SELECT id, model_name FROM models ORDER BY model_name ASC");
$modelResModal = $conn->query("SELECT m.id, m.model_name 
                            FROM models m 
                            LEFT JOIN users u_owner ON m.owner_id = u_owner.id
                            LEFT JOIN department d ON u_owner.department_id = d.id
                            WHERE d.id = 1
                            ORDER BY model_name ASC");

$role_id = $_SESSION['role_id'] ?? 'Guest'; // trigger access menu
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
    <title>Connectify | Line Report</title>
    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">
    <style>
        #lineReportTable td,
        #lineReportTable th {
            white-space: normal !important;   
            /* word-wrap: break-word !important;  */
            /* word-break: break-word !important; */
             /* padding: 8px 12px; */
        }
        .remark-text {
            white-space: pre-line;
        }

    </style>
</head>

<body>
    <?php
    require_once '../../layout/header.php';
    require_once '../../layout/sidebar.php';
    ?>
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/connectify-web/pages/dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">Line Report Section 1</li>
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
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createSec1Modal">
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
                <div class="row g-3 px-0 mt-2 mb-2 align-items-end">
                    <div class="col-md-12 d-flex align-items-end justify-content-end">
                        <input type="search" id="customSearchBox" class="form-control" placeholder="Search..." style="max-width: 250px;">
                    </div>
                </div>
                <div class="row">
                    <div class="col-xxl-12">
                        <div class="card stretch stretch-full">
                            <div class="card-header">
                                <h5 class="card-title">Line Report Section 1</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"> </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-2 m-2">
                                <div class="table-responsive">
                                    <div id="alertLineReportSec1"></div>
                                    <table id="lineReportTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>No</th>
                                                <th>Department</th>
                                                <th>Date</th>
                                                <th>Shift</th>
                                                <th>Model Name</th>
                                                <th>Line Area</th>
                                                <th>Remark</th>
                                                <th>Reported By</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lineReportTableBody">
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
        require_once '../../layout/footer.php';
        ?>
    </main>

    <!-- add -->
    <div class="modal fade" id="createSec1Modal" tabindex="-1" aria-labelledby="createSec1ModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="createSec1ModalLabel">Create New Report</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="lineReportSec1" class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <select id="modelSelect" class="form-select" required>
                                <option value="">-----</option>
                                <?php while ($row = $modelResModal->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['model_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line Area</label>
                            <input type="text" id="lineInput" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" id="date" class="form-control" required
                                max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                        </div>
                         <div class="col-md-6">
                            <label class="form-label">Shift</label>
                            <select id="shift" class="form-select" required>
                                <option value="">-----</option>
                                <option value="Day Shift">Day Shift</option>
                                <option value="Second Shift">Second Shift</option>
                                <option value="Night Shift">Night Shift</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea id="remark" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-secondary px-4 me-3" id="clear" data-dismiss="modal">Clear</button>
                            <button type="button" id="saveLineReport" class="btn btn-success px-4">Save</button>
                        </div>
                        <input type="hidden" id="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- delete -->
    <div class="modal fade" id="deleteModalSec1" tabindex="-1" aria-labelledby="deleteModalSec1Label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalSec1Label">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this report?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteSec1">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <script src="/connectify-web/assets/vendors/js/vendors.min.js"></script>
     <!-- vendors.min.js {always must need to be top} -->
    <script src="/connectify-web/assets/vendors/js/dataTables.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/dataTables.bs5.min.js"></script>
    <script src="/connectify-web/assets/js/leads-init.min.js"></script>

    <script src="/connectify-web/assets/vendors/js/apexcharts.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/select2.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/select2-active.min.js"></script>
    <script src="/connectify-web/assets/vendors/js/jquery.time-to.min.js "></script>
    <script src="/connectify-web/assets/js/common-init.min.js"></script>
     <script src="assets/js/projects-init.min.js"></script>
    <script src="/connectify-web/assets/js/widgets-tables-init.min.js"></script>
    <script src="/connectify-web/assets/js/theme-customizer-init.min.js"></script>
    <script src="/connectify-web/assets/bootstrap-5/DataTables/dataTables.buttons.min.js"></script>
    <script src="/connectify-web/assets/bootstrap-5/DataTables/jszip.min.js"></script>
    <script src="/connectify-web/assets/bootstrap-5/DataTables/buttons.html5.min.js"></script>

    <script src="/connectify-web/pages/js/dashboard.js"></script>
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
        $(document).ready(function() {
            const lineReportTable = $('#lineReportTable').DataTable({
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/LineReportController.php?type=sec1',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json.success ? json.data : [];
                    }
                },
                columns: [{
                        data: null,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    {
                        data: 'department_name',
                         render: function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'date'
                    },
                    {
                        data: 'shift',
                        render: function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'model_name',
                         render: function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'line_area',
                         render: function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: 'remark',
                        render: function (data, type, row) {
                            if (!data) return '';
                            return `<div class="remark-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    {
                        data: 'report_user',
                        render: function (data, type, row) {
                            return data;
                        }
                    },
                    {
                        data: null,
                         className: 'text-center',
                        render: function(data, type, row) {
                            const isAdmin = [1, 2].includes(parseInt(LOGGED_USER_ROLE));
                            const isOwner = parseInt(row.user_id) === parseInt(LOGGED_USER_ID);

                            if (!isAdmin && !isOwner) {
                                return ''; 
                            }
                            return `
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <a href="#" class="btn btn-sm btn-danger btn-delete-server"
                                    data-id="${row.id}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteModalSec1">
                                    <i class="feather-trash"></i>
                                </a>
                            </div>
                        `;
                        }
                    }
                ],
                columnDefs: [{
                    targets: [8],
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                }],
                autoWidth: false,
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
                },
            });

            $('#customSearchBox').on('keyup', function() {
                lineReportTable.search(this.value).draw();
            });

            let deleteServer = null;
            $(document).on('click', '.btn-delete-server', function(e) {
                e.preventDefault();
                deleteServer = $(this).data('id');
            });
            $('#btnConfirmDeleteSec1').on('click', function() {
                if (!deleteServer) return;

                $.ajax({
                    url: '/connectify-web/controllers/LineReportController.php',
                    type: 'DELETE',
                    data: JSON.stringify({
                        id: deleteServer
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#deleteModalSec1').modal('hide');

                        if (response.success) {
                            lineReportTable.ajax.reload(null, false);
                            showAlert('Success', response.message, 'success');
                        } else {
                            showAlert('Failed', response.message, 'danger');
                        }

                        deleteServer = null;
                    },
                    error: function(xhr) {
                        $('#deleteModalSec1').modal('hide');
                        showAlert('Error', xhr.statusText, 'danger');
                        deleteServer = null;
                    }
                });
            });

            function showAlert(title, message, type) {
                const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                    <strong>${title}:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                $('#alertLineReportSec1').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
        });

        $('#clear').click(function() {
            $('#lineReportSec1')[0].reset();
            $('#message-container').html('');
        });
    </script>
    <script>
        $('#modelSelect').change(function() {
            const model_id = $(this).val();
            if (!model_id) {
                $('#lineInput').val('');
                $('#targetInput').val('');
                return;
            }

            $.ajax({
                url: '/connectify-web/pages/reports/get-data.php',
                type: 'POST',
                data: {
                    action: 'getLine',
                    model_id
                },
                dataType: 'json',
                // success: function(data) {
                //     $('#lineInput').val(data.line_area || '');
                //     $('#targetInput').val(data.output_target || '');
                // },
                success: function(res) {
                    if (res.success && res.data) {
                        $('#lineInput').val(res.data.line_area);
                    } else {
                        $('#lineInput').val('');
                    }
                },
                error: function(xhr) {
                    console.error("Error getting Line Area:", xhr.responseText);
                }
            });
        });

         // save data
        $('#saveLineReport').click(function() {
            const payload = {
                shift: $('#shift').val(),
                date: $('#date').val(),
                model_id: $('#modelSelect').val(),
                remark: $('#remark').val(),
                user_id: $('#user_id').val(),
            };
            console.log(payload);

            $.ajax({
                url: '/connectify-web/controllers/LineReportController.php',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                success: function(response) {
                    $('#createSec1Modal').modal('hide');

                    if (response.success) {
                        $('#alertLineReportSec1').html(
                            `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                        );

                        setTimeout(() => {
                            $('.alert').alert('close');
                            $('#createSec1Modal').modal('hide');
                        }, 1500);

                        $('#lineReportSec1')[0].reset();
                        $('#lineReportTable').DataTable().ajax.reload(null, false);


                    } else {
                        $('#message-container').html(`
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>`);
                    }
                },
                error: function(xhr) {
                    let msg = "Unexpected error";
                    try {
                        let res = JSON.parse(xhr.responseText);
                        if (res.message) msg = res.message;
                    } catch {}
                    $('#message-container').html(`
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        ${msg}
                    </div>`);
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 1500);
                }
            });
        });
    </script>
</body>

</html>