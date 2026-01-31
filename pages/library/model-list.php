<?php
include '../../config.php';
session_start();

$users_owner = [];
$userQuery = "SELECT id, name, role_id FROM users WHERE role_id = '2' OR role_id = '1' ORDER BY name ASC";
$userResult = $conn->query($userQuery);
if ($userResult->num_rows > 0) {
    while ($row = $userResult->fetch_assoc()) {
        $users_owner[] = $row;
    }
}

$users_member = [];
$userQuery = "SELECT id, name, role_id FROM users WHERE role_id = '3' ORDER BY name ASC";
$userResult = $conn->query($userQuery);
if ($userResult->num_rows > 0) {
    while ($row = $userResult->fetch_assoc()) {
        $users_member[] = $row;
    }
}

$stations = [];
$stationQuery = "SELECT id, station_name FROM stations ORDER BY station_name ASC";
$stationResult = $conn->query($stationQuery);
if ($stationResult->num_rows > 0) {
    while ($row = $stationResult->fetch_assoc()) {
        $stations[] = $row;
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
    <title>Connectify | Model</title>
    
    <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/dataTables.bs5.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">
    <!-- <link rel="shortcut icon" type="image/x-icon" href="/connectify-web/assets/images/logo.png" />
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/vendors.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/select2-theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/vendors/css/jquery.time-to.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/theme.min.css">
    <link rel="stylesheet" type="text/css" href="/connectify-web/assets/css/footer.css">
    <link href="/connectify-web/assets/public/vendor/DataTables/datatables.min.css" rel="stylesheet"> -->

    <style>
        /* Station & Device → boleh wrap */
        /* #modelTable td.col-station {
            white-space: normal !important;
            word-break: break-word;
        } */

        /* Owner & Members → TIDAK BOLEH melebar */
        /* #modelTable td.col-owner,
        #modelTable td.col-members {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        } */

     /* Membuat semua cell di #modelTable wrap */
        #modelTable td,
        #modelTable th {
            white-space: normal !important;
            /* membolehkan teks ke baris berikutnya */
            /* word-wrap: break-word !important; 
            word-break: break-word !important;
             padding: 8px 12px; */
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
                        <li class="breadcrumb-item">Models</li>
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
                            <a href="javascript:void(0);" class="btn btn-md btn-primary" data-bs-toggle="modal" data-bs-target="#createModelModal">
                                <i class="feather-plus me-2"></i>
                                <span>Add Model</span>
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
                                <h5 class="card-title">Models</h5>
                                <div class="card-header-action">
                                    <div class="card-header-btn">
                                        <div data-bs-toggle="tooltip" title="Refresh">
                                            <a href="javascript:void(0);" class="avatar-text avatar-xs bg-warning" data-bs-toggle="refresh"></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body custom-card-action p-2 m-2">
                                <div class="table-responsive">
                                    <div id="alertModelContainer"></div>
                                    <table id="modelTable" class="table table-hover mb-0">
                                        <thead>
                                            <tr class="border-b">
                                                <th scope="row">No</th>
                                                <th>Model Name</th>
                                                <th>Line Area</th>
                                                <th class="wrap-text">Station & Device</th>
                                                <th>Owner</th>
                                                <th>Members</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
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

    <!-- add model -->
    <div class="modal fade" id="createModelModal" tabindex="-1" aria-labelledby="createModelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 35rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createModelModalLabel">Create New Model</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <form id="modelForm" class="row g-3">
                        <div class="col-md-6">
                            <label>Model Name</label>
                            <input type="text" id="model_name" name="model_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Line Area</label>
                            <input type="text" id="lineArea" name="lineArea" class="form-control" required>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="userOwner" class="form-label">Owner</label>
                            <select id="userOwner" name="userOwner" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($users_owner as $owner): ?>
                                    <option value="<?= $owner['id'] ?>">
                                        <?= htmlspecialchars($owner['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label class="form-label">Members</label>
                            <select id="userMembers" name="userMembers[]" class="form-select" required>
                                <option value="">-----</option>
                                <?php foreach ($users_member as $member): ?>
                                    <option value="<?= $member['id'] ?>">
                                        <?= htmlspecialchars($member['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label">Selected Members</label>
                            <div id="selectedMembersContainer" class="d-flex flex-wrap gap-2 border p-2" style="min-height: 50px;"></div>
                        </div>
                        <div class="col-12 d-flex justify-content-center mt-4">
                            <button type="button" class="btn btn-secondary px-4 me-3" id="clear" data-dismiss="modal">Clear</button>
                            <button type="button" id="saveNewModel" class="btn btn-success px-4">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- edit -->
    <div class="modal fade" id="editModelModal" tabindex="-1" aria-labelledby="editModelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" style="width: 45rem;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModelModalLabel">Edit Model</h5>
                    <button id="closeX" class="close" type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="message-container"></div>
                    <nav>
                        <div class="nav nav-tabs" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-model-station-tab" data-bs-toggle="tab" data-bs-target="#nav-model-station" type="button" role="tab" aria-controls="nav-model-station" aria-selected="true">Model/Station</button>
                            <button class="nav-link" id="nav-device-tab" data-bs-toggle="tab" data-bs-target="#nav-device" type="button" role="tab" aria-controls="nav-device" aria-selected="false">Device</button>
                        </div>
                    </nav>

                    <br>
                    <input type="hidden" id="edit-id" name="id">
                    <input type="hidden" id="edit-model-id" name="model_id">

                    <!-- edit model and device -->
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="nav-model-station" role="tabpanel" aria-labelledby="nav-model-station-tab">
                            <div id="edit-message-container"></div>
                            <form id="modelStationForm" class="row g-3">
                                <div class="col-md-4">
                                    <label>Model Name</label>
                                    <input type="text" id="editModel_name" name="editModel_name" class="form-control" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Line Area</label>
                                    <input type="text" id="editLineArea" name="editLineArea" class="form-control" required>
                                </div>
                                <!-- <div class="col-md-4">
                                <label>Owner</label>
                                <input type="text" id="editUserOwner" name="editUserOwner" class="form-control" required readonly>
                            </div> -->
                                <div class="col-md-4">
                                    <label class="">Owner</label>
                                    <select id="editUserOwner" name="editUserOwner" class="form-select">
                                        <option value="">-----</option>
                                        <?php foreach ($users_owner as $owner): ?>
                                            <option value="<?= $owner['id'] ?>">
                                                <?= htmlspecialchars($owner['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Members</label>
                                    <select id="editUserMembers" name="editUserMembers" class="form-select" required>
                                        <option value="">-----</option>
                                        <?php foreach ($users_member as $member): ?>
                                            <option value="<?= $member['id'] ?>">
                                                <?= htmlspecialchars($member['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-8">
                                    <label class="form-label">Selected Members</label>
                                    <div id="EditSelectedMembersContainer" class="d-flex flex-wrap gap-2 border p-2" style="min-height: 50px;"></div>
                                </div>
                                <!-- <div class="col-md-12">
                                <label>Stations</label>
                                <textarea type="text" id="modelStation" name="modelStation" class="form-control"></textarea>
                            </div> -->
                                <div class="col-6">
                                    <label>Stations</label>
                                    <div class="d-flex gap-2 mb-2">
                                        <input type="text" id="newStationName" class="form-control" placeholder="Enter new station name">
                                        <button type="button" id="addStationBtn" class="btn btn-primary">Add</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div id="stationList" class="d-flex flex-wrap gap-2 border p-2" style="min-height: 50px;"></div>
                                </div>
                                <div class="col-12 d-flex justify-content-center mt-4">
                                    <button type="button" id="editSaveStationModel" class="btn btn-success px-4">Save</button>
                                </div>
                            </form>
                        </div>

                        <!-- Edit device -->
                        <div class="tab-pane fade" id="nav-device" role="tabpanel" aria-labelledby="nav-device-tab">
                            <div id="edit-message-container"></div>
                            <form id="modelDeviceForm" class="row g-3">
                                <div class="col-md-4">
                                    <label>Model Name</label>
                                    <input type="text" id="editDeviceModelName" name="editDeviceModelName" class="form-control" required readonly>
                                </div>
                                <div class="col-md-4">
                                    <label>Line Area</label>
                                    <input type="text" id="editDeviceLineArea" name="editDeviceLineArea" class="form-control" required readonly>
                                </div>
                                <!-- <div class="col-md-4">
                                <label class="form-label">Stations</label>
                                <select id="editDeviceStation" name="deviceStation" class="form-select" required>
                                    <option value="">-----</option>
                                    <?php foreach ($stations as $station): ?>
                                        <option value="<?= $station['id'] ?>">
                                            <?= htmlspecialchars($station['station_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div> -->
                                <div class="col-md-4">
                                    <label class="">Stations</label>
                                    <select id="editDeviceStation" name="deviceStation" class="form-select" required>
                                        <option value="">-----</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label>Device</label>
                                    <div class="d-flex gap-2 mb-2">
                                        <input type="text" id="newDeviceName" class="form-control" placeholder="Enter new device name">
                                        <button type="button" id="addDeviceBtn" class="btn btn-primary">Add</button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div id="deviceList" class="d-flex flex-wrap gap-2 border p-2" style="min-height: 50px;"></div>
                                </div>
                                <div class="col-12 d-flex justify-content-center mt-4">
                                    <button type="button" id="editSaveDeviceModel" class="btn btn-success px-4">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
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
    <script src="/connectify-web/assets/bootstrap-5/DataTables/buttons.html5.min.js"></script>>
   <!-- <script src="/connectify-web/assets/public/vendor/DataTables/datatables.min.js"></script> -->
    <script src="/connectify-web/pages/js/dashboard.js"></script>
    <script>
        setInterval(() => {
            fetch("/connectify-web/update_activity.php");
        }, 60000);
    </script>

    <script>
        $(document).ready(function() {
            const modelTable = $('#modelTable').DataTable({
                dom: 'lrtip',
                ajax: {
                    url: '/connectify-web/controllers/ModelController.php',
                    type: 'GET',
                    dataSrc: function(json) {
                        return json.success ? json.data : [];
                        //  return json.data || [];
                    }
                },
                columns: [{
                        data: null,
                        className: 'text-center',
                        render: function(data, type, row, meta) {
                            return meta.row + 1;
                        }
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
                        data: null,
                        render: function(data, type, row) {
                            if (!row.stations || row.stations.length === 0) return '-';

                            let output = '';
                            row.stations.forEach(station => {
                                if (station.devices && station.devices.length > 0) {
                                    let deviceNames = station.devices.map(d => d.device_name).join(',  ');
                                    output += `<strong>${station.station_name}</strong>: ${deviceNames}<br>`;
                                } else {
                                    output += `<strong>${station.station_name}</strong>: -<br>`;
                                }
                            });
                            return output;
                        }
                    },
                    {
                        data: 'owner',
                        className: 'text-center',
                    },
                    {
                        data: 'members',
                        className: 'text-center',
                    },
                    {
                        data: null,
                        render: function(data, type, row) {
                            // console.log(row)
                            return `
                            <div class="d-flex justify-content-center align-items-center gap-1">
                                <a href="#" class="btn btn-sm btn-warning btn-edit-model"
                                    data-id="${row.id}"
                                    data-model_name="${row.model_name}"
                                    data-line_area="${row.line_area}"
                                    data-owner="${row.owner}"
                                    data-owner_id="${row.owner_id}"
                                    data-members="${row.members}"
                                    data-member_ids="${row.member_ids}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModelModal">
                                    <i class="feather-edit"></i></a>
                            </div>
                        `;
                        }
                    }
                ],
                // ordering: false,
                columnDefs: [{
                    targets: -1,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    },
                    
                }],
                autoWidth: false,
                // columnDefs: [
                //     { targets: -1, orderable: false },
                //     { targets: 1, width: "10%"},
                //     { targets: 3, width: "35%" },
                //     { targets: 4, width: "15%" }, 
                //     { targets: 5, width: "15%" }  
                // ],
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

            $('#customSearchBox').on('keyup', function() {
                modelTable.search(this.value).draw();
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            let selectedMembers = new Map();

            function renderSelectedMembers() {
                const container = $('#selectedMembersContainer');
                container.empty();
                selectedMembers.forEach((name, id) => {
                    const badge = $(`
                <span class="badge bg-info text-dark d-flex align-items-center">
                    ${name}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-2" aria-label="Remove"></button>
                </span>
            `);
                    badge.find('button').click(() => {
                        selectedMembers.delete(id);
                        renderSelectedMembers();
                        $(`#userMembers option[value="${id}"]`).prop('selected', false);
                    });
                    container.append(badge);
                });
            }

            $('#userMembers').on('change', function() {
                const selectedOptions = Array.from(this.selectedOptions);
                selectedOptions.forEach(opt => {
                    selectedMembers.set(opt.value, opt.text);
                });
                renderSelectedMembers();
            });

            // $('#saveNewModel').on('click', function () {
            $('#saveNewModel').click(function() {
                const payload = {
                    model_name: $('#model_name').val().trim(),
                    line_area: $('#lineArea').val().trim(),
                    owner_id: $('#userOwner').val(),
                    members: Array.from(selectedMembers.keys())
                };

                // console.log(payload);

                $.ajax({
                    url: '/connectify-web/controllers/ModelController.php',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        $('#createModelModal').modal('hide');

                        if (response.success) {
                            $('#alertModelContainer').html(
                                `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                            );

                            setTimeout(() => {
                                $('.alert').alert('close');
                                $('#createModelModal').modal('hide');
                            }, 1500);

                            $('#modelForm')[0].reset();
                            selectedMembers.clear();
                            renderSelectedMembers();
                            $('#modelTable').DataTable().ajax.reload(null, false);

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
                $('#modelForm')[0].reset();
                selectedMembers.clear();
                renderSelectedMembers();
                $('#message-container').html('');
                // $('#userMembers').val(null).trigger('change');
                $('#userMembers').val('');
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            let stations = [];
            let devices = [];
            let selectedMembers = new Map();

            $('#editModelModal').on('show.bs.modal', function(event) {
                const button = $(event.relatedTarget);
                const modal = $(this);
                const model_id = button.data('id');
                const model_name = button.data('model_name');
                const line_area = button.data('line_area');
                const owner = button.data('owner');
                const owner_id = button.data('owner_id');
                const members = button.data('members') ? button.data('members').split(',') : [];
                const stationData = button.data('stations') ? button.data('stations').split(',') : [];
                const deviceData = button.data('devices') ? button.data('devices').split(',') : [];

                modal.find('#edit-id').val(model_id);
                modal.find('#editModel_name').val(model_name);
                modal.find('#editDeviceModelName').val(model_name);
                modal.find('#editLineArea').val(line_area);
                modal.find('#editDeviceLineArea').val(line_area);

                // modal.find('#editUserOwner').val(owner);
                modal.find('#editUserOwner').val(owner_id);
                // modal.find('#editUserOwner').val(parseInt(owner_id));

                // Stations
                const memberContainer = $('#EditSelectedMembersContainer');
                memberContainer.empty();
                selectedMembers.clear();
                members.forEach(name => {
                    const trimmed = name.trim();
                    selectedMembers.set(trimmed, trimmed);
                    memberContainer.append(`
                <span class="badge bg-info text-dark me-2 mb-2">
                    ${trimmed}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 remove-member" data-name="${trimmed}"></button>
                </span>
            `);
                });

                memberContainer.off('click', '.remove-member').on('click', '.remove-member', function() {
                    $(this).closest('span').remove();
                });

                stations = [...stationData];
                const stationContainer = $('#stationList');
                stationContainer.empty();
                stations.forEach(st => {
                    const trimmed = st.trim();
                    stationContainer.append(`
                <span class="badge bg-secondary text-light me-2 mb-2">
                    ${trimmed}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 remove-station" data-name="${trimmed}"></button>
                </span>
            `);
                });

                stationContainer.off('click', '.remove-station').on('click', '.remove-station', function() {
                    const name = $(this).data('name');
                    stations = stations.filter(s => s !== name);
                    $(this).closest('span').remove();
                    renderDeviceStations(); // update device select
                });

                devices = button.data('devices') || []; // array of device objects if any
                renderDeviceList();

                // Fetch stations per model
                $.ajax({
                    url: '/connectify-web/pages/library/get-data2.php',
                    type: 'POST',
                    data: {
                        model_id
                    },
                    dataType: 'json',
                    success: function(res) {
                        const select = $('#editDeviceStation');
                        select.empty().append('<option value="">-----</option>');
                        res.stations.forEach(st => {
                            select.append(`<option value="${st.id}">${st.station_name}</option>`);
                        });
                    },
                    error: function(xhr) {
                        console.error('Error fetching stations:', xhr.responseText);
                    }
                });
            });

            // Render Device Station Select
            function renderDeviceStations() {
                const select = $('#editDeviceStation');
                select.empty().append('<option value="">-----</option>');
                stations.forEach((st, idx) => {
                    select.append(`<option value="${st}">${st}</option>`);
                });
            }

            // Render Selected Members
            function renderEditSelectedMembers() {
                const container = $('#EditSelectedMembersContainer');
                container.empty();
                selectedMembers.forEach((name, id) => {
                    const badge = $(`
                <span class="badge bg-info text-dark d-flex align-items-center me-2 mb-2">
                    ${name}
                    <button type="button" class="btn-close btn-close-white btn-sm ms-2 remove-member" data-id="${id}" aria-label="Remove"></button>
                </span>
            `);
                    badge.find('button').click(() => {
                        selectedMembers.delete(id);
                        renderEditSelectedMembers();
                        $(`#editUserMembers option[value="${id}"]`).prop('selected', false);
                    });
                    container.append(badge);
                });
            }

            $('#editUserMembers').on('change', function() {
                const selectedOptions = Array.from(this.selectedOptions);
                selectedOptions.forEach(opt => {
                    selectedMembers.set(opt.value, opt.text);
                });
                renderEditSelectedMembers();
            });

            // Render Device List
            function renderDeviceList() {
                const container = $('#deviceList');
                container.empty();
                devices.forEach(d => {
                    container.append(`
                <span class="badge bg-info text-dark me-2 mb-2">
                    ${d.device_name} (${d.station})
                    <button type="button" class="btn-close btn-close-white btn-sm ms-1 remove-device" data-id="${d.id}"></button>
                </span>
            `);
                });

                container.off('click', '.remove-device').on('click', '.remove-device', function() {
                    const id = parseInt($(this).data('id'));
                    devices = devices.filter(d => d.id !== id);
                    renderDeviceList();
                });
            }

            // Add Station
            $('#addStationBtn').on('click', function() {
                const newStation = $('#newStationName').val().trim();
                if (!newStation) return alert('Please enter a station name');
                if (stations.includes(newStation)) return alert('Station already exists');

                stations.push(newStation);
                $('#stationList').append(`
            <span class="badge bg-info text-dark me-2 mb-2">
                ${newStation}
                <button type="button" class="btn-close btn-close-white btn-sm ms-1 remove-station" data-name="${newStation}"></button>
            </span>
        `);
                $('#newStationName').val('');
                renderDeviceStations();
            });

            // Add Device
            $('#addDeviceBtn').on('click', function() {
                const deviceName = $('#newDeviceName').val().trim();
                const stationId = $('#editDeviceStation').val();
                const stationText = $('#editDeviceStation option:selected').text();

                if (!deviceName || !stationId) {
                    return alert('Please select a station and enter device name');
                }
                // const isDuplicate = devices.some(d => d.device_name.toLowerCase() === deviceName.toLowerCase());
                // if (isDuplicate) {
                //     return alert('Device already exists');
                // }
                const deviceNames = devices.map(d => d.device_name);
                if (deviceNames.includes(deviceName)) {
                    return alert('Device already exists');
                }

                const deviceId = Date.now(); // temporary unique ID
                devices.push({
                    id: deviceId,
                    device_name: deviceName,
                    station_id: stationId,
                    station: stationText
                });

                renderDeviceList();
                $('#newDeviceName').val('');
            });

            // Save Model + Stations
            $('#editSaveStationModel').on('click', function(e) {
                e.preventDefault();
                // $(document).on('click', '#editSaveStationModel', function (e) {
                //     e.preventDefault();

                const id = $('#edit-id').val().trim();
                const line_area = $('#editLineArea').val().trim();
                const owner_id = $('#editUserOwner').val().trim();
                const membersArray = Array.from(selectedMembers.keys())
                    .map(v => parseInt(v, 10))
                    .filter(v => !isNaN(v));

                // console.log(membersArray); 
                const payload = {
                    id,
                    line_area,
                    owner_id,
                    members: membersArray,
                    stations
                };
                // console.log(payload)
                $.ajax({
                    url: '/connectify-web/controllers/ModelController.php?action=update',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        const res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.success) {
                            $('#editModelModal').modal('hide');
                            // $('#modelTable').DataTable().ajax.reload(null, false);
                            // alert(res.message);
                            $('#alertModelContainer').html(
                                `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                            );

                            setTimeout(() => {
                                $('.alert').alert('close');
                            }, 1500);

                            $('#modelStationForm')[0].reset();
                            $('#modelTable').DataTable().ajax.reload(null, false);
                        } else {
                            // alert(res.message);
                            $('#edit-message-container').html(`
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
                        $('#edit-message-container').html(`
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        ${msg}
                    </div>`);
                        setTimeout(() => {
                            $('.alert').alert('close');
                        }, 1500);
                    }
                });
            });

            // Save Devices
            $('#editSaveDeviceModel').on('click', function() {
                const model_id = $('#edit-id').val().trim();
                const line_area = $('#editDeviceLineArea').val().trim();
                const owner_id = $('#editUserOwner').val().trim();
                // if (devices.length === 0) {
                //     return alert('Please add at least one device.');
                // }
                const payload = {
                    id: model_id,
                    owner_id: owner_id,
                    line_area: line_area,
                    devices: devices.map(d => ({
                        station_id: d.station_id,
                        device_name: d.device_name
                    }))
                };
                // console.log(payload)
                $.ajax({
                    url: '/connectify-web/controllers/ModelController.php?action=update',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(payload),
                    success: function(response) {
                        const res = typeof response === 'string' ? JSON.parse(response) : response;
                        if (res.success) {
                            $('#editModelModal').modal('hide');
                            // $('#modelTable').DataTable().ajax.reload(null, false);
                            // alert(res.message);
                            $('#alertModelContainer').html(
                                `<div class="alert alert-success alert-dismissible fade show" role="alert">
                        ${response.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                            );

                            setTimeout(() => {
                                $('.alert').alert('close');
                            }, 1500);

                            $('#modelDeviceForm')[0].reset();
                            // selectedMembers.clear();
                            // renderSelectedMembers();
                            $('#modelTable').DataTable().ajax.reload(null, false);
                        } else {
                            $('#edit-message-container').html(`
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
                        $('#edit-message-container').html(`
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        ${msg}
                    </div>`);
                        setTimeout(() => {
                            $('.alert').alert('close');
                        }, 1500);
                    }
                });
            });
        });
    </script>

</body>

</html>                    