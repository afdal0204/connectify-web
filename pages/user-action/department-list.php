<?php
include '../../config.php';
session_start();

$role_id = $_SESSION['role_id'] ?? 'Guest';
$department_id = $_SESSION['department_id'];
$deptRemark = $_SESSION['deptRemark'];
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
    <title>Connectify | Department</title>
    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">

    <style>
          #departmentTable td,
        #departmentTable th {
            white-space: normal !important;
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
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/connectify-web/pages/dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">Department</li>
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
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createDepartmentModal">
                                <i class="feather-plus me-2"></i>
                                <span>Add Department</span>
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
                                <h5 class="card-title">Department List</h5>
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
                                    <div id="alertDepartmentContainer"></div>
                                    <table id="departmentTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>No</th>
                                                <th>Department Name</th>
                                                <th>Description</th>
                                                <th>Remark</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="departmentTableBody">

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

    <!-- add -->
    <div class="modal fade" id="createDepartmentModal" tabindex="-1" aria-labelledby="createDepartmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 30rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createDepartmentModalLabel">New Department</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="departmentForm" class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Department Name</label>
                            <input id="department" class="form-control" rows="2"></input>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea id="description" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Remark</label>
                            <input id="remark" class="form-control" rows="2"></input>
                        </div>

                        <!-- <input type="hidden" id="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>"> -->
                    </form>
                </div>

                <div class="modal-footer col-12 d-flex justify-content-center mt-4">
                    <button type="button" class="btn btn-secondary" id="clear">Clear</button>
                    <button type="button" class="btn btn-success" id="save">Save</button>
                </div>

            </div>
        </div>
    </div>

    <!-- delete -->
    <div class="modal fade" id="deleteDepartmentModal" tabindex="-1" aria-labelledby="deleteDepartmentLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteDepartmentLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Department?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteDepartment">Delete</button>
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
    <!-- <script src="assets/js/projects-init.min.js"></script> -->
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
            const departmentTable = $('#departmentTable').DataTable({
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/DepartmentController.php',
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
                        data: 'department_name'
                    },
                    {
                        data: 'description'
                    },
                    {
                        data: 'remark'
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
                                <a href="#" class="btn btn-sm btn-danger btn-delete-error-code" data-id="${row.id}">
                                    <i class="feather-trash"></i>
                                </a>
                            </div>
                        `;
                        }
                    }
                ],
                columnDefs: [{
                        targets: 4,
                        orderable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        },

                    },
                    {
                        targets: -1,
                        // visible: CURRENT_USER_ROLE_ID == 1
                    },
                ],
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
                departmentTable.search(this.value).draw();
            });

            $(document).on('click', '.btn-delete-error-code', function (e) {
                e.preventDefault();

                const departmentId = $(this).data('id');
                console.log(departmentId)

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "Are you sure?",
                    text: "You want to delete this department!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed || result.value === true) {
                        $.ajax({
                            url: '/connectify-web/controllers/DepartmentController.php',
                            type: 'DELETE',
                            data: JSON.stringify({
                                id: departmentId
                             }),
                            contentType: 'application/json',
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    swalWithBootstrapButtons.fire(
                                        "Deleted!",
                                        response.message,
                                        "success"
                                    );

                                    $('#departmentTable')
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
                            error: function (xhr) {
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
                $('#alertDepartmentContainer').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
        });
    </script>
    <script>
        $('#save').click(function() {
            const payload = {
                department_name: $('#department').val(),
                description: $('#description').val(),
                remark: $('#remark').val()
            };
            // console.log(payload);

            $.ajax({
                url: '/connectify-web/controllers/DepartmentController.php',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                success: function(response) {
                    $('#createDepartmentModal').modal('hide');
                    if (response.success) {

                        showSuccessToast(response.message);

                        $('#departmentForm')[0].reset();
                        $('#departmentTable').DataTable().ajax.reload(null, false);

                    } else {
                        showErrorToast(response.message);
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

        $('#clear').click(function() {
            $("#departmentForm")[0].reset();
        });
    </script>
</body>

</html>