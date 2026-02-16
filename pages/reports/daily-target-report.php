<?php
include './../../config.php';
session_start();

$modelRes = $conn->query("SELECT id, model_name FROM models ORDER BY model_name ASC");
$modelResModal = $conn->query("SELECT id, model_name FROM models ORDER BY model_name ASC");

$uphtargets = [];
// $uphQuery = $conn->query("SELECT id, uph_status_name FROM uph_status ORDER BY uph_status_name DESC");
$uphQuery = "SELECT id, uph_status_name FROM uph_status ORDER BY uph_status_name DESC";
$uphResult = $conn->query($uphQuery);
if ($uphResult->num_rows > 0) {
    while ($row = $uphResult->fetch_assoc()) {
        $uphtargets[] = $row;
    }
}

$role_id = $_SESSION['role_id'] ?? 'Guest';
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
    <title>Connectify | Daily Target Report</title>

    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">

    <style>
        /* Membuat semua cell di #modelTable wrap */
        #dailyTargetReportTable td,
        #dailyTargetReportTable th {
            white-space: normal !important;
            /* membolehkan teks ke baris berikutnya */
            /* word-wrap: break-word !important; 
            word-break: break-word !important;
             padding: 8px 12px; */
        }
        .remark-text {
            white-space: pre-line;
        }
        /* Opsional: batasi lebar kolom tertentu supaya wrap lebih cepat */
        /* #modelTable td:nth-child(4),
        #modelTable th:nth-child(4) {
            max-width: 200px;
        }

        #modelTable td:nth-child(6),
        #modelTable th:nth-child(6) {
            max-width: 220px;
        } */
    </style>

</head>

<body>
    <?php
    require_once '../layout/header.php';
    require_once '../layout/sidebar.php';
    ?>
    <main class="nxl-container">
        <div class="nxl-content">
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/connectify-web/pages/dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">Daily Target Reports</li>
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
                                <!-- <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
                                    <i class="feather-filter me-2"></i>
                                    <span>Filter</span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Role" checked="checked">
                                            <label class="custom-control-label c-pointer" for="Role">Role</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Team" checked="checked">
                                            <label class="custom-control-label c-pointer" for="Team">Team</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Email" checked="checked">
                                            <label class="custom-control-label c-pointer" for="Email">Email</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Member" checked="checked">
                                            <label class="custom-control-label c-pointer" for="Member">Member</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-item">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="Recommendation" checked="checked">
                                            <label class="custom-control-label c-pointer" for="Recommendation">Recommendation</label>
                                        </div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="feather-plus me-3"></i>
                                        <span>Create New</span>
                                    </a>
                                    <a href="javascript:void(0);" class="dropdown-item">
                                        <i class="feather-filter me-3"></i>
                                        <span>Manage Filter</span>
                                    </a>
                                </div> -->
                            </div>
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createTargetReportModal">
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
                                <h5 class="card-title">Daily Target Reports</h5>
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
                                    <div id="alertTargetReportContainer"></div>
                                    <table id="dailyTargetReportTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>No</th>
                                                <th>Date</th>
                                                <th>Model</th>
                                                <th>Line</th>
                                                <th>Owner</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th>Target</th>
                                                <th>Output</th>
                                                <th>Gap</th>
                                                <th>Remarks</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="dailyTargetReportTableBody">
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
        require_once '../layout/footer.php'
        ?>
    </main>

    <div class="modal fade" id="createTargetReportModal" tabindex="-1" aria-labelledby="createTargetReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 40rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createTargetReportModalLabel">Create New Report</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="dailyTargetReportForm" class="row g-3">
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
                        <div class="col-md-4">
                            <label class="form-label">Target</label>
                            <input type="number" name="target" id="targetInput" class="form-control" min="0" max="9999" step="1" value="" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Output</label>
                            <input type="number" name="output" id="outputInput" class="form-control" min="0" max="9999" step="1" value="" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gap</label>
                            <input type="text" name="gap" id="gapInput" class="form-control" min="0" max="9999" step="1" value="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status UPH</label>
                            <select id="statusUphSelect" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($uphtargets as $uphtarget): ?>
                                    <option value="<?= $uphtarget['id'] ?>">
                                        <?= htmlspecialchars($uphtarget['uph_status_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" id="date" class="form-control" required
                                max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea id="remark" class="form-control" rows="2"></textarea>

                            <!-- <label class="form-label">Upload Image <small class="text-muted">(Optional)</small></label>
                            <input type="file"
                                name="remark_image"
                                id="remarkImage"
                                class="form-control"
                                accept="image/*"> -->
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

    <!-- edit report -->
    <div class="modal fade" id="editTargetReportModal" tabindex="-1" aria-labelledby="editTargetReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 40rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editReportModalLabel">Edit Report</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-edit-container"></div>
                    <form id="editdailyTargetReportForm" class="row g-3">
                        <input type="hidden" id="edit-id" name="id">
                        <input type="hidden" id="edit-model-id" name="model_id">
                        <!-- <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <select id="edimodelSelect" class="form-select" required>
                                <option value="">-----</option>
                                <?php while ($row = $modelRes->fetch_assoc()): ?>
                                    <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['model_name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div> -->
                        <div class="col-md-6">
                            <label class="form-label">Model</label>
                            <input type="text" id="edimodelSelect" class="form-control" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Line Area</label>
                            <input type="text" id="editlineInput" class="form-control" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Target</label>
                            <input type="number" name="target" id="edittargetInput" class="form-control" min="0" max="9999" step="1" value="" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Output</label>
                            <input type="number" name="output" id="editoutputInput" class="form-control" min="0" max="9999" step="1" value="" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Gap</label>
                            <input type="text" name="gap" id="editgapInput" class="form-control" min="0" max="9999" step="1" value="">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status UPH</label>
                            <select id="editstatusUphSelect" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($uphtargets as $uphtarget): ?>
                                    <option value="<?= $uphtarget['id'] ?>">
                                        <?= htmlspecialchars($uphtarget['uph_status_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Date</label>
                            <input type="date" id="editdate" class="form-control" required
                                max="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Remarks</label>
                            <textarea id="editremark" class="form-control" rows="2"></textarea>
                        </div>

                        <input type="hidden" id="edituser_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
                    </form>
                </div>
                <div class="modal-footer col-12 d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal" id="clear">Cancel</button>
                    <button type="button" class="btn btn-success" id="saveEditTargetReport">Save</button>
                </div>

            </div>
        </div>
    </div>
                                    
    <div class="modal fade" id="deleteModalTargetReport" tabindex="-1" aria-labelledby="deleteModalTargetReportLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalTargetReportLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this report?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteDailyTargetReport">Delete</button>
                </div>
            </div>
        </div>
    </div>

     <script src="/connectify-web/assets/js/apps-storage-init.min.js"></script>
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
        function showSuccessToast(message) {
            Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                }
            }).fire({
                icon: "success",
                title: message
            });
        }
        function showErrorToast(message) {
            Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            }).fire({
                icon: "error",
                title: message
            });
        }

        $(document).ready(function() {
            const dailytargetTable = $('#dailyTargetReportTable').DataTable({
                // dom: 'Brtip',
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/DailyTargetReportController.php',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json.success ? json.data : [];
                    }
                },
                // searching: false,
                columns: [{
                        data: null,
                        className: 'text-center',
                        render: (data, type, row, meta) => meta.row + 1
                    },
                    {
                        data: 'date',
                        className: 'text-center'
                    },
                    {
                        data: 'model_name',
                        className: 'text-center',
                    },
                    {
                        data: 'line_area',
                        className: 'text-center',
                    },
                    {
                        data: 'owner_name',
                        className: 'text-center',
                    },
                    {
                        data: 'department_name',
                        className: 'text-center',
                    },
                    {
                        data: 'uph_status_name',
                        className: 'text-center',
                        render: function(data, type, row) {
                            let colorClass = '';

                            switch (data.toLowerCase()) {
                                case 'target':
                                    colorClass = 'badge bg-success';
                                    break;
                                case 'not target':
                                    colorClass = 'badge bg-danger';
                                    break;
                                case 'not running':
                                    colorClass = 'badge bg-warning text-dark';
                                    break;
                                default:
                                    colorClass = 'badge bg-secondary';
                            }

                            return `<span class="${colorClass}">${data}</span>`;
                        }
                    },
                    {
                        data: 'target',
                        render: function(data, type, row) {
                            if (!data || data === "0" || data === 0) {
                                return '-'
                            }
                            return data;
                        },
                        className: 'text-center',
                    },
                    {
                        data: 'output',
                        render: function(data, type, row) {
                            if (!data || data === "0" || data === 0) {
                                return '-'
                            }
                            return data;
                        },
                        className: 'text-center',
                    },
                    {
                        data: 'gap',
                        render: function(data, type, row) {
                            if (!data) {
                                return '-'
                            }
                            return data;
                        },
                        className: 'text-center',
                    },
                    {
                        data: 'remark',
                        className: '',
                        render: function (data, type, row) {
                            if (!data) return '';
                            return `<div class="remark-text">${$('<div>').text(data).html()}</div>`;
                        }
                    },
                    // {
                    //     data: 'report_user',
                    //     className: 'text-center',
                    // },

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
                                <a href="#" class="btn btn-sm btn-warning btn-edit-target-report"
                                    data-id="${row.id}"
                                    data-uph_status_id="${row.uph_status_id}"
                                    data-model_name="${row.model_name}"
                                    data-line_area="${row.line_area}"
                                    data-target="${row.target}"
                                    data-output="${row.output}"
                                    data-gap="${row.gap}"
                                    data-remark="${row.remark}"
                                    data-date="${row.date}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editTargetReportModal">
                                    <i class="feather-edit"></i></a>
                                    
                                <a href="#" class="btn btn-sm btn-danger btn-delete-target-report" 
                                    data-id="${row.id}" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModalTargetReport">
                                    <i class="feather-trash"></i>
                                </a>
                            </div>
                        `;
                        }
                    }
                ],
                // ordering: false,
                columnDefs: [{
                    targets: 11,
                    orderable: false
                }],
                autoWidth: false,
                pageLength: 10,
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

            $('#customSearchBox').on('keyup', function() {
                dailytargetTable.search(this.value).draw();
            });

           

            let deleteReportId = null;

            $(document).on('click', '.btn-delete-target-report', function(e) {
                e.preventDefault();
                deleteReportId = $(this).data('id');
            });
            
            // $(document).on('click', '.btn-delete-target-report', function (e) {
            //     e.preventDefault();

            //     const reportId = $(this).data('id');
            //     console.log(reportId)

            //     const swalWithBootstrapButtons = Swal.mixin({
            //         customClass: {
            //             confirmButton: "btn btn-danger m-1",
            //             cancelButton: "btn btn-secondary m-1"
            //         },
            //         buttonsStyling: false
            //     });

            //     swalWithBootstrapButtons.fire({
            //         title: "Are you sure?",
            //         text: "You want to delete this report!",
            //         icon: "warning",
            //         showCancelButton: true,
            //         confirmButtonText: "Yes, delete it!",
            //         cancelButtonText: "No, cancel!",
            //         reverseButtons: true
            //     }).then((result) => {
            //         if (result.isConfirmed) {
            //             $.ajax({
            //                 url: '/connectify-web/controllers/DailyTargetReportController.php',
            //                 type: 'DELETE',
            //                 data: JSON.stringify({ 
            //                     id: reportId
            //                  }),
            //                 contentType: 'application/json',
            //                 dataType: 'json', 
            //                 success: function (response) {
            //                     console.log("Response:", response);

            //                     if (response.success) {
            //                         swalWithBootstrapButtons.fire(
            //                             "Deleted!",
            //                             response.message,
            //                             "success"
            //                         );

            //                         $('#dailyTargetReportTable')
            //                             .DataTable()
            //                             .ajax.reload(null, false);

            //                     } else {

            //                         swalWithBootstrapButtons.fire(
            //                             "Failed!",
            //                             response.message,
            //                             "error"
            //                         );
            //                     }
            //                 },
            //                 error: function (xhr) {
            //                     swalWithBootstrapButtons.fire(
            //                         "Error!",
            //                         "Something went wrong!",
            //                         "error"
            //                     );
            //                 }
            //             });

            //         } else if (result.dismiss === Swal.DismissReason.cancel) {

            //             swalWithBootstrapButtons.fire(
            //                 "Cancelled",
            //                 "Your data is safe :)",
            //                 "error"
            //             );
            //         }
            //     });
            // });

            $('#btnConfirmDeleteDailyTargetReport').on('click', function() {
                if (!deleteReportId) return;

                $.ajax({
                    url: '/connectify-web/controllers/DailyTargetReportController.php',
                    type: 'DELETE',
                    data: JSON.stringify({
                        id: deleteReportId
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#deleteModalTargetReport').modal('hide');
                        showSuccessToast(response.message);

                        // if (response.success) {
                        //     dailytargetTable.ajax.reload(null, false);
                        //     showAlert('Success', response.message, 'success');
                        // } else {
                        //     showAlert('Failed', response.message, 'danger');
                        // }
                        dailytargetTable.ajax.reload(null, false);
                        deleteReportId = null;
                    },
                    error: function(xhr) {
                        $('#deleteModalTargetReport').modal('hide');
                        showAlert('Error', xhr.statusText, 'danger');
                        deleteReportId = null;
                    }
                });
            });

            function showAlert(title, message, type) {
                const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                <strong>${title}:</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`;
                $('#alertTargetReportContainer').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
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
                        $('#targetInput').val(res.data.output_target);
                    } else {
                        $('#lineInput').val('');
                        $('#targetInput').val('');
                    }
                },
                error: function(xhr) {
                    console.error("Error getting Line Area:", xhr.responseText);
                }
            });
        });

        document.getElementById('targetInput').addEventListener('input', calculateGap);
        document.getElementById('outputInput').addEventListener('input', calculateGap);

        function calculateGap() {
            const target = parseFloat(document.getElementById('targetInput').value) || 0;
            const output = parseFloat(document.getElementById('outputInput').value) || 0;
            let gap = output - target;
            if (gap > 0) {
                gap = `+${gap}`;
                document.getElementById('gapInput').value = gap;
            } else {

                document.getElementById('gapInput').value = gap;
            }
        }

        // save data
        $('#save').click(function() {
            const payload = {
                date: $('#date').val(),
                model_id: $('#modelSelect').val(),
                uph_status_id: $('#statusUphSelect').val(),
                target: $('#targetInput').val(),
                output: $('#outputInput').val(),
                gap: $('#gapInput').val(),
                remark: $('#remark').val(),
                user_id: $('#user_id').val(),
            };
            // console.log(payload);

            $.ajax({
                url: '/connectify-web/controllers/DailyTargetReportController.php',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                success: function(response) {
                    $('#createTargetReportModal').modal('hide');
                    if (response.success) {

                        showSuccessToast(response.message);

                        $('#dailyTargetReportForm')[0].reset();
                        $('#dailyTargetReportTable').DataTable().ajax.reload(null, false);

                    } else {
                        showErrorToast(response.message);
                    }

                    // if (response.success) {
                    //     $('#alertTargetReportContainer').html(
                    //         `<div class="alert alert-success alert-dismissible fade show" role="alert">
                    //         ${response.message}
                    //     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    // </div>`
                    //     );

                    //     setTimeout(() => {
                    //         $('.alert').alert('close');
                    //         $('#createTargetReportModal').modal('hide');
                    //     }, 1500);

                    //     $('#dailyTargetReportForm')[0].reset();
                    //     $('#dailyTargetReportTable').DataTable().ajax.reload(null, false);


                    // } else {
                    //     $('#message-container').html(`
                    //     <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    //         ${response.message}
                    //         <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    //     </div>`);
                    // }
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

        // display data to edit form
        $('#editTargetReportModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);

            var id = button.data('id');
            var date = button.data('date');
            var model_id = button.data('model_id');
            var model_name = button.data('model_name');
            var line_area = button.data('line_area');
            var uph_status_id = button.data('uph_status_id');
            var uph_status_name = button.data('uph_status_name');
            var target = button.data('target');
            var output = button.data('output');
            var gap = button.data('gap');
            var remark = button.data('remark');
            var user_id = button.data('user_id');

            var modal = $(this);
            modal.find('#edit-id').val(id);
            modal.find('#edit-model-id').val(model_id);
            modal.find('#edimodelSelect').val(model_name);
            modal.find('#editlineInput').val(line_area);
            modal.find('#editdate').val(date);
            modal.find('#edittargetInput').val(target);
            modal.find('#editoutputInput').val(output);
            modal.find('#editgapInput').val(gap);
            modal.find('#editstatusUphSelect').val(uph_status_id);
            modal.find('#editremark').val(remark);
            modal.find('#edituser_id').val(user_id);

            modal.find('#message-edit-container').html('');
        });

        document.getElementById('edittargetInput').addEventListener('input', calculateGapEdit);
        document.getElementById('editoutputInput').addEventListener('input', calculateGapEdit);

        function calculateGapEdit() {
            const target = parseFloat(document.getElementById('edittargetInput').value) || 0;
            const output = parseFloat(document.getElementById('editoutputInput').value) || 0;
            let gap = output - target;
            if (gap > 0) {
                gap = `+${gap}`;
                document.getElementById('editgapInput').value = gap;
            } else {

                document.getElementById('editgapInput').value = gap;
            }
        }
        // Edit save 
        $('#saveEditTargetReport').on('click', function(e) {
            e.preventDefault();
            var modal = $('#editTargetReportModal');

            var payload = {
                id: modal.find('#edit-id').val(),
                date: modal.find('#editdate').val(),
                uph_status_id: modal.find('#editstatusUphSelect').val(),
                target: modal.find('#edittargetInput').val(),
                output: modal.find('#editoutputInput').val(),
                gap: modal.find('#editgapInput').val(),
                remark: modal.find('#editremark').val()
            };

            // console.log(payload);

            $.ajax({
                url: '/connectify-web/controllers/DailyTargetReportController.php',
                type: 'PUT',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    $('#editTargetReportModal').modal('hide');
                    if (response.success) {

                        showSuccessToast(response.message);

                        $('#editdailyTargetReportForm')[0].reset();
                        $('#dailyTargetReportTable').DataTable().ajax.reload(null, false);

                    } else {
                        showErrorToast(response.message);
                    }

                    // if (response.success) {
                    //     $('#alertTargetReportContainer').html(
                    //         `<div class="alert alert-success alert-dismissible fade show" role="alert">
                    //         ${response.message}
                    //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    //     </div>`
                    //     );

                    //     setTimeout(() => {
                    //         $('.alert').alert('close');
                    //         $('#editTargetReportModal').modal('hide');
                    //     }, 1500);

                    //     // Reset form
                    //     $('#editdailyTargetReportForm')[0].reset();
                    //     $('#dailyTargetReportTable').DataTable().ajax.reload(null, false);
                    // } else {
                    //     $('#message-edit-container').html(
                    //         `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                    //         ${response.message}
                    //         <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    //     </div>`
                    //     );
                    // }
                },
                error: function(xhr, status, error) {
                    let msg = "Unexpected error";
                    try {
                        let res = JSON.parse(xhr.responseText);
                        if (res.message) {
                            msg = res.message;
                        }
                    } catch (e) {
                        msg = xhr.responseText;
                    }

                    $('#message-edit-container').html(
                        `<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        ${msg}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                    );

                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 1500);
                }
            });

        });


        $('#clear').click(function() {
            $('#dailyTargetReportForm')[0].reset();
            createTargetReportModal.hide();
        });
    </script>
</body>

</html>