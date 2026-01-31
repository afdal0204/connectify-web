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

$departments = [];
$deptQuery = "SELECT id, department_name FROM department";
$deptResult = $conn->query($deptQuery);
if ($deptResult->num_rows > 0) {
    while ($row = $deptResult->fetch_assoc()) {
        $departments[] = $row;
    }
}

$roles = [];
$roleQuery = "SELECT id, role_name FROM user_role";
$roleResult = $conn->query($roleQuery);
if ($roleResult->num_rows > 0) {
    while ($row = $roleResult->fetch_assoc()) {
        $roles[] = $row;
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
    <title>Connectify | User</title>
    
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
            <!-- [ page-header ] start -->
            <div class="page-header">
                <div class="page-header-left d-flex align-items-center">
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/connectify-web/pages/dashboard.php">Home</a></li>
                        <li class="breadcrumb-item">Users</li>
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
                            </div>
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                                <i class="feather-plus me-2"></i>
                                <span>Add User</span>
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
                                <h5 class="card-title">Users</h5>
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
                                    <div id="alertUserContainer"></div>
                                    <table id="userTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th scope="row">No</th>
                                                <th>Name</th>
                                                <th>Work ID</th>
                                                <th>Department</th>
                                                <th>Role</th>
                                                <th>Last Activity</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="userTableBody">
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

    <!-- add user -->
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createUserModalLabel">Create New User</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="userForm" class="row g-3">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Work ID</label>
                            <input type="text" id="work_id" name="work_id" class="form-control" required>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label>Password</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="department_id" class="form-label">Department</label>
                            <select id="department_id" name="department_id" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>">
                                        <?= htmlspecialchars($department['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Role</label>
                            <select id="role_id" name="role_id" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-12 d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-secondary px-4 me-3" id="clear" data-dismiss="modal">Clear</button>
                            <button type="button" id="saveNewUser" class="btn btn-success px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- edit user -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editModalUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalUserLabel">Edit User</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-edit-container"></div>
                    <form id="edituserForm" class="row g-3">
                        <div class="col-md-6">
                            <label>Name</label>
                            <input type="text" id="editname" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Work ID</label>
                            <input type="text" id="editwork_id" name="work_id" class="form-control" required readonly>
                        </div>
                        <div class="col-md-6">
                            <label>New Password</label>
                            <input type="password" id="passwordEdit" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Confirm Password</label>
                            <input type="password" id="passwordEditConfirm" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="department_id" class="form-label">Department</label>
                            <select id="editdepartment_id" name="department_id" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>">
                                        <?= htmlspecialchars($department['department_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Role</label>
                            <select id="editrole_id" name="role_id" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>">
                                        <?= htmlspecialchars($role['role_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="modal-footer col-12 d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            <button type="button" id="saveEditUser" class="btn btn-success px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- delete user -->
    <div class="modal fade" id="deleteModalUser" tabindex="-1" aria-labelledby="deleteModalUserLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalUserLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this user?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDeleteUser">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- view user -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="viewUserModalLabel">User Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Name:</strong> <span id="userName"></span></li>
                        <li class="list-group-item"><strong>Work ID:</strong> <span id="userWorkId"></span></li>
                        <li class="list-group-item"><strong>Department:</strong> <span id="userDept"></span></li>
                        <li class="list-group-item"><strong>Role:</strong> <span id="userRole"></span></li>
                        <li class="list-group-item"><strong>Model:</strong> <span id="userModel"></span></li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            const userTable = $('#userTable').DataTable({
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/UserController.php',
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
                        data: 'name'
                    },
                    {
                        data: 'work_id'
                    },
                    {
                        data: 'department'
                    },
                    {
                        data: 'role_name'
                    },
                    {
                        data: 'last_activity',
                        className: 'text-center'
                    },
                    {
                        data: 'is_online',
                        render: function(data) {
                            if (data == 1) {
                                return `<span class="badge bg-success" style="font-size:13px;">
                                        <i class="fas fa-circle"></i> Online
                                    </span>`;
                            } else {
                                return `<span class="badge bg-secondary" style="font-size:13px;">
                                        <i class="fas fa-circle"></i> Offline
                                    </span>`;
                            }
                        }
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            return `
                            <div class="d-flex justify-content-center align-items-center gap-1">
                            <a href="#" class="btn btn-sm btn-info btn-see-user"
                                data-work_id="${row.work_id}"
                                data-name="${row.name}"
                                data-department="${row.department}"                              
                                data-role="${row.role_name}"
                                data-model="${row.all_models ?? ''}"
                                data-bs-toggle="modal"
                                data-bs-target="#viewUserModal"><i class="feather-eye"></i></a>
                                
                            <a href="#" class="btn btn-sm btn-warning btn-edit-user"
                                data-work_id="${row.work_id}"
                                data-name="${row.name}"
                                data-department_id="${row.department_id}"
                                data-role_id="${row.role_id}"
                                data-bs-toggle="modal" 
                                data-bs-target="#editUserModal">
                                <i class="feather-edit"></i>
                            </a>
                            <a href="#" class="btn btn-sm btn-danger btn-delete-user" 
                                data-work_id="${row.work_id}"
                                data-bs-toggle="modal" 
                                data-bs-target="#deleteModalUser">
                                <i class="feather-trash"></i>
                            </a>
                             </div>
                        `;
                        }
                    }
                ],
                columnDefs: [{
                    targets: 7,
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
                userTable.search(this.value).draw();
            });

            let deleteUser = null;
            $(document).on('click', '.btn-delete-user', function(e) {
                e.preventDefault();
                deleteUser = $(this).data('work_id');
            });
            $('#btnConfirmDeleteUser').on('click', function() {
                if (!deleteUser) return;

                $.ajax({
                    url: '/connectify-web/controllers/UserController.php',
                    type: 'DELETE',
                    data: JSON.stringify({
                        work_id: deleteUser
                    }),
                    contentType: 'application/json',
                    success: function(response) {
                        $('#deleteModalUser').modal('hide');

                        if (response.success) {
                            userTable.ajax.reload(null, false);
                            showAlert('Success', response.message, 'success');
                        } else {
                            showAlert('Failed', response.message, 'danger');
                        }

                        deleteUser = null;
                    },
                    error: function(xhr) {
                        $('#deleteModalUser').modal('hide');
                        showAlert('Error', xhr.statusText, 'danger');
                        deleteUser = null;
                    }
                });
            });

            function showAlert(title, message, type) {
                const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show mt-3" role="alert">
                    <strong>${title}:</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                $('#alertUserContainer').html(alertHtml);

                setTimeout(() => {
                    $('.alert').alert('close');
                }, 1500);
            }
        });
    </script>

    <script>
        $('#saveNewUser').click(function() {
            var payload = {
                name: $('#name').val(),
                work_id: $('#work_id').val(),
                password: $('#password').val(),
                department_id: $('#department_id').val(),
                role_id: $('#role_id').val()
            };
            console.log(payload);
            $.ajax({
                url: '/connectify-web/controllers/UserController.php',
                type: 'POST',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    $('#createUserModal').modal('hide');
                    if (response.success) {
                        // alert(response.message);
                        $('#alertUserContainer').html(
                            `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                        );

                        setTimeout(() => {
                            $('.alert').alert('close');
                            $('#createUserModal').modal('hide');
                        }, 1500);

                        // Reset form
                        $('#userForm')[0].reset();
                        $('#userTable').DataTable().ajax.reload(null, false);
                    } else {
                        $('#message-container').html(
                            `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${response.message}
                        </div>`
                        );
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
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                    );
                    setTimeout(() => {
                        $('.alert').alert('close');
                    }, 1500);
                }
            });
        });

        // view user
        $('#viewUserModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var workId = button.data('work_id');
            var name = button.data('name');
            var department = button.data('department');
            var roleId = button.data('role');

            var modal = $(this);
            modal.find('#userWorkId').text(workId);
            modal.find('#userName').text(name);
            modal.find('#userDept').text(department);
            modal.find('#userRole').text(roleId);

            var models = button.data('model');
            $('#userModel').text(models ? models : "-");
        });

        $('#editUserModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var workId = button.data('work_id');
            var name = button.data('name');
            var departmentId = button.data('department_id');
            var roleId = button.data('role_id');

            var modal = $(this);
            modal.find('#editwork_id').val(workId);
            modal.find('#editname').val(name);
            modal.find('#editdepartment_id').val(departmentId);
            modal.find('#editrole_id').val(roleId);

            modal.find('#passwordEdit').val('');
            modal.find('#passwordEditConfirm').val('');
            modal.find('#message-edit-container').html('');
        });

        $('#saveEditUser').click(function() {
            $('#editUserModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var workId = button.data('work_id');
                var modal = $(this);
                modal.find('#editwork_id').val(workId);
            });

            var payload = {
                name: $('#editname').val(),
                work_id: $('#editwork_id').val(),
                password: $('#passwordEditConfirm').val(),
                department_id: $('#editdepartment_id').val(),
                role_id: $('#editrole_id').val()
            };
            // console.log(payload);
            $.ajax({
                url: '/connectify-web/controllers/UserController.php',
                type: 'PUT',
                data: JSON.stringify(payload),
                contentType: 'application/json',
                success: function(response) {
                    $('#editUserModal').modal('hide');
                    if (response.success) {
                        $('#alertUserContainer').html(
                            `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                        );

                        setTimeout(() => {
                            $('.alert').alert('close');
                            $('#editUserModal').modal('hide');
                        }, 1500);

                        // Reset form
                        $('#edituserForm')[0].reset();
                        $('#userTable').DataTable().ajax.reload(null, false);
                    } else {
                        $('#message-edit-container').html(
                            `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                        );
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

        $('#clear').click(function() {
            $('#userForm')[0].reset();
            $('#message-container').html('');
        });
    </script>
</body>

</html>