<?php
include '../../config.php';
session_start();

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
    <title>Connectify | Error Code</title>
    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">

    <style>
          #errorCodeTable td,
        #errorCodeTable th {
            white-space: normal !important;
            /* membolehkan teks ke baris berikutnya */
            /* word-wrap: break-word !important; 
            word-break: break-word !important;
             padding: 8px 12px; */
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
                        <li class="breadcrumb-item">Error Code</li>
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
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createErrorCodeModal">
                                <i class="feather-plus me-2"></i>
                                <span>Add Error Code</span>
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
                                <h5 class="card-title">Error Code</h5>
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
                                    <div id="alertErrorCodeContainer"></div>
                                    <table id="errorCodeTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>No</th>
                                                <th>Error Code</th>
                                                <th>Symptom </th>
                                                <th>Created By</th>
                                                <th>Work ID</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="errorCodeTableBody">

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <!-- <div class="card-footer">
                                <ul class="list-unstyled d-flex align-items-center gap-2 mb-0 pagination-common-style">
                                    <li>
                                        <a href="javascript:void(0);"><i class="bi bi-arrow-left"></i></a>
                                    </li>
                                    <li><a href="javascript:void(0);" class="active">1</a></li>
                                    <li><a href="javascript:void(0);">2</a></li>
                                    <li>
                                        <a href="javascript:void(0);"><i class="bi bi-dot"></i></a>
                                    </li>
                                    <li><a href="javascript:void(0);">8</a></li>
                                    <li><a href="javascript:void(0);">9</a></li>
                                    <li>
                                        <a href="javascript:void(0);"><i class="bi bi-arrow-right"></i></a>
                                    </li>
                                </ul>
                            </div> -->
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
    <div class="modal fade" id="createErrorCodeModal" tabindex="-1" aria-labelledby="createErrorCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 30rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createErrorCodeModalLabel">New Error Code</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="errorCodeForm" class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Error Code</label>
                            <input id="errorCode" class="form-control" rows="2"></input>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Symptom</label>
                            <textarea id="symptom" class="form-control" rows="2"></textarea>
                        </div>

                        <input type="hidden" id="user_id" value="<?= htmlspecialchars($_SESSION['user_id'] ?? '') ?>">
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
    <div class="modal fade" id="deleteErrorCodeModal" tabindex="-1" aria-labelledby="deleteErrorCodeLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteErrorCodeLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this Error Code?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteErrorCode">Delete</button>
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
        setInterval(() => {
            fetch("/connectify-web/update_activity.php");
        }, 60000);
    </script>

    <script>
        $(document).ready(function() {
            const errorCodeTable = $('#errorCodeTable').DataTable({
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/ErrorCodeController.php',
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
                        data: 'error_code'
                    },
                    {
                        data: 'symptom'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'work_id'
                    },
                    {
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <a href="#" class="btn btn-sm btn-danger btn-delete-error-code" 
                                    data-id="${row.error_id}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteErrorCodeModal">
                                    <i class="feather-trash"></i>
                                </a>
                            </div>
                        `;
                        }
                    }
                ],
                columnDefs: [{
                        targets: 5,
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
                errorCodeTable.search(this.value).draw();
            });

            let deleteErrorCode = null;

            $(document).on('click', '.btn-delete-error-code', function(e) {
                e.preventDefault();
                deleteErrorCode = $(this).data('id');
            });

            $('#btnConfirmDeleteErrorCode').on('click', function() {
                if (!deleteErrorCode) return;
                console.log(deleteErrorCode)
                $.ajax({
                    url: '/connectify-web/controllers/ErrorCodeController.php',
                    type: 'DELETE',
                    data: JSON.stringify({
                        id: deleteErrorCode
                    }),
                    contentType: 'application/json',
                    processData: false,
                    success: function(response) {
                        $('#deleteErrorCodeModal').modal('hide');

                        if (response.success) {
                            errorCodeTable.ajax.reload(null, false);
                            showAlert('Success', response.message, 'success');
                        } else {
                            showAlert('Failed', response.message, 'danger');
                        }

                        deleteErrorCode = null;
                    },
                    error: function(xhr) {
                        $('#deleteErrorCodeModal').modal('hide');
                        showAlert('Error', xhr.statusText, 'danger');
                        deleteErrorCode = null;
                        errorCodeTable.ajax.reload(null, false);
                    }
                });
            });

            function showAlert(title, message, type) {
                const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                    <strong>${title}:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                $('#alertErrorCodeContainer').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
        });
    </script>
    <script>
        $('#save').click(function() {
            const payload = {
                error_code: $('#errorCode').val(),
                symptom: $('#symptom').val(),
                user_id: $('#user_id').val()
            };
            // console.log(payload);

            $.ajax({
                url: '/connectify-web/controllers/ErrorCodeController.php',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json; charset=UTF-8',
                dataType: 'json',
                success: function(response) {
                    $('#createErrorCodeModal').modal('hide');

                    if (response.success) {
                        $('#alertErrorCodeContainer').html(
                            `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                        );

                        setTimeout(() => {
                            $('.alert').alert('close');
                            $('#createErrorCodeModal').modal('hide');
                        }, 1500);

                        $('#errorCodeForm')[0].reset();
                        $('#errorCodeTable').DataTable().ajax.reload(null, false);


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

        $('#clear').click(function() {
            $("#errorCodeForm")[0].reset();
        });
    </script>
</body>

</html>