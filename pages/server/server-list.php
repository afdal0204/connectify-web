<?php
include '../../config.php';
// include '../init.php';
session_start();
// include '../auth_check.php';

if (!isset($_SESSION['user_id'])) {
    $redirect = urlencode($_SERVER['REQUEST_URI']);
    header("Location: /connectify-web/login.php?redirect={$redirect}");
    exit();
}

$locations = [];
$locQuery = "SELECT id, location_name FROM server_location ORDER BY location_name ASC";
$locResult = $conn->query($locQuery);
if ($locResult->num_rows > 0) {
    while ($row = $locResult->fetch_assoc()) {
        $locations[] = $row;
    }
}

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
    <title>Connectify | Server</title>
    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">
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
                        <li class="breadcrumb-item">Servers</li>
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
                            <!-- <div class="dropdown filter-dropdown">
                                <a class="btn btn-md btn-light-brand" data-bs-toggle="dropdown" data-bs-offset="0, 10" data-bs-auto-close="outside">
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
                                </div>
                            </div> -->
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createServerModal">
                                <i class="feather-plus me-2"></i>
                                <span>Add Server</span>
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
                                <h5 class="card-title">Servers</h5>
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
                                    <div id="alertServerContainer"></div>
                                    <table id="serverTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th>No</th>
                                                <th>Server IP</th>
                                                <th>Asset Number</th>
                                                <th>Location</th>
                                                <th>Remark</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="serverTableBody">
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
    <div class="modal fade" id="createServerModal" tabindex="-1" aria-labelledby="createServerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="createServerModalLabel">Create New Server</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="serverForm" class="row g-3">
                        <div class="col-md-6">
                            <label>Server IP</label>
                            <input type="text" id="server_ip" name="server_ip" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Asset Number</label>
                            <input type="text" id="asset_number" name="asset_number" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label for="location_id" class="form-label">Location</label>
                            <select id="location_id" name="location_id" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= $location['id'] ?>">
                                        <?= htmlspecialchars($location['location_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Remark</label>
                            <textarea id="remark" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="col-12 d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-secondary px-4 me-3" id="clear" data-dismiss="modal">Clear</button>
                            <button type="button" id="saveNewServer" class="btn btn-success px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- edit -->
    <div class="modal fade" id="editServerModal" tabindex="-1" aria-labelledby="editModalServerLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="editModalServerLabel">Edit Server</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-edit-container"></div>
                    <form id="editServerForm" class="row g-3">
                        <input type="hidden" id="server-edit-id" name="id">
                        <div class="col-md-6">
                            <label>Server IP</label>
                            <input type="text" id="editserver_ip" name="editserver_ip" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Asset Number</label>
                            <input type="text" id="editasset_number" name="editasset_number" class="form-control" required>
                        </div>
                        <div class="col-md-5">
                            <label for="editlocation_id" class="form-label">Location</label>
                            <select id="editlocation_id" name="editlocation_id" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($locations as $location): ?>
                                    <option value="<?= $location['id'] ?>">
                                        <?= htmlspecialchars($location['location_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-7">
                            <label class="form-label">Remark</label>
                            <textarea id="editremark" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="modal-footer col-12 d-flex justify-content-center mt-4">
                            <!-- <button type="button" class="btn btn-danger px-4 me-3" id="cancel" data-dismiss="modal">Cancel</button> -->
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="saveEditUser" class="btn btn-success px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- delete -->
    <div class="modal fade" id="deleteModalServer" tabindex="-1" aria-labelledby="deleteModalServerLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalServerLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this server?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteServer">Delete</button>
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
            const serverTable = $('#serverTable').DataTable({
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/ServerController.php',
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
                        data: 'server_ip'
                    },
                    {
                        data: 'asset_number'
                    },
                    {
                        data: 'location_name'
                    },
                    {
                        data: 'remark'
                    },
                    {
                        data: null,
                         className: 'text-center',
                        render: function(data, type, row) {
                            return `
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <a href="#" class="btn btn-sm btn-warning btn-edit-server"
                                    data-id="${row.id}"
                                    data-server_ip="${row.server_ip}"
                                    data-asset_number="${row.asset_number}"
                                    data-location_id="${row.location_id}"
                                    data-remark="${row.remark}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editServerModal">
                                    <i class="feather-edit"></i>
                                </a>
                                <a href="#" class="btn btn-sm btn-danger btn-delete-server" data-id="${row.id}">
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
                    }
                }],
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
                },
            });

            $('#customSearchBox').on('keyup', function() {
                serverTable.search(this.value).draw();
            });

            $(document).on('click', '.btn-delete-server', function (e) {
                e.preventDefault();

                const serverId = $(this).data('id');
                console.log(serverId)

                const swalWithBootstrapButtons = Swal.mixin({
                    customClass: {
                        confirmButton: "btn btn-success m-1",
                        cancelButton: "btn btn-secondary m-1"
                    },
                    buttonsStyling: false
                });

                swalWithBootstrapButtons.fire({
                    title: "Are you sure?",
                    text: "You want to delete this server!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel!",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed || result.value === true) {
                        $.ajax({
                            url: '/connectify-web/controllers/ServerController.php',
                            type: 'DELETE',
                            data: JSON.stringify({ 
                                id: serverId
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

                                    $('#serverTable')
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
                $('#alertServerContainer').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
        });

        $('#clear').click(function() {
            $('#serverForm')[0].reset();
            $('#message-container').html('');
        });
    </script>
    <script>
        $('#saveNewServer').click(function() {
            var payload = {
                server_ip: $('#server_ip').val(),
                asset_number: $('#asset_number').val(),
                location_id: $('#location_id').val(),
                remark: $('#remark').val()
            };
            console.log(payload);
            $.ajax({
                url: '/connectify-web/controllers/ServerController.php',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    $('#createServerModal').modal('hide');

                    if (response.success) {
                        showSuccessToast(response.message);
                        $('#serverForm')[0].reset();
                        $('#serverTable').DataTable().ajax.reload(null, false);
                    } else {
                        showErrorToast(response.message);
                    }
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

                    $('#message-container').html(
                        `<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        ${msg}
                    </div>`
                    );
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 1500);
                }
            });
        });

        $('#editServerModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);

            var id = button.data('id');
            var server_ip = button.data('server_ip');
            var asset_number = button.data('asset_number');
            var locationId = button.data('location_id');
            var remark = button.data('remark');

            var modal = $(this);
            modal.find('#server-edit-id').val(id);
            modal.find('#editserver_ip').val(server_ip);
            modal.find('#editasset_number').val(asset_number);
            modal.find('#editlocation_id').val(locationId);
            modal.find('#editremark').val(remark);
        });

        $('#saveEditUser').click(function() {
            var payload = {
                id: $('#server-edit-id').val(),
                server_ip: $('#editserver_ip').val(),
                asset_number: $('#editasset_number').val(),
                location_id: $('#editlocation_id').val(),
                remark: $('#editremark').val()
            };
            console.log(payload);
            $.ajax({
                url: '/connectify-web/controllers/ServerController.php',
                type: 'PUT',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    $('#editServerModal').modal('hide');
                    if (response.success) {
                        showSuccessToast(response.message);
                        $('#serverForm')[0].reset();
                        $('#serverTable').DataTable().ajax.reload(null, false);
                    } else {
                        showErrorToast(response.message);
                    }
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
    </script>
</body>

</html>