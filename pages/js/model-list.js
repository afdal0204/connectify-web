// new
$(document).ready(function () {
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

    $('#userMembers').on('change', function () {
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

        console.log(payload);

        $.ajax({
            url: '/connectify-web/controllers/ModelController.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (response) {
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
            error: function (xhr) {
                let msg = "Unexpected error";
                try {
                    let res = JSON.parse(xhr.responseText);
                    if (res.message) msg = res.message;
                } catch { }
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

    $('#clear').click(function () {
        $('#modelForm')[0].reset();
        selectedMembers.clear();
        renderSelectedMembers();
        $('#message-container').html('');
        // $('#userMembers').val(null).trigger('change');
        $('#userMembers').val('');
    });
});

// edit
$(document).ready(function () {
    let stations = [];
    let devices = [];
    let selectedMembers = new Map();

    $('#editModelModal').on('show.bs.modal', function (event) {
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

        memberContainer.off('click', '.remove-member').on('click', '.remove-member', function () {
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

        stationContainer.off('click', '.remove-station').on('click', '.remove-station', function () {
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
            data: { model_id },
            dataType: 'json',
            success: function (res) {
                const select = $('#editDeviceStation');
                select.empty().append('<option value="">-----</option>');
                res.stations.forEach(st => {
                    select.append(`<option value="${st.id}">${st.station_name}</option>`);
                });
            },
            error: function (xhr) {
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

    $('#editUserMembers').on('change', function () {
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

        container.off('click', '.remove-device').on('click', '.remove-device', function () {
            const id = parseInt($(this).data('id'));
            devices = devices.filter(d => d.id !== id);
            renderDeviceList();
        });
    }

    // Add Station
    $('#addStationBtn').on('click', function () {
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
    $('#addDeviceBtn').on('click', function () {
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
        devices.push({ id: deviceId, device_name: deviceName, station_id: stationId, station: stationText });

        renderDeviceList();
        $('#newDeviceName').val('');
    });

    // Save Model + Stations
    // $('#editSaveStationModel').on('click', function (e) {
    //     e.preventDefault();
    $(document).on('click', '#editSaveStationModel', function (e) {
        e.preventDefault();
        console.log("clicked!");
        const id = $('#edit-id').val().trim();
        const line_area = $('#editLineArea').val().trim();      
        const owner_id = $('#editUserOwner').val().trim();
        // const membersArray = Array.from(selectedMembers.keys());
        const membersArray = Array.from(selectedMembers.keys())
        .map(v => parseInt(v, 10))
        .filter(v => !isNaN(v));

        // console.log(membersArray); 
        const payload = { id, line_area, owner_id, members: membersArray, stations };
        
        $.ajax({
            url: '/connectify-web/controllers/ModelController.php?action=update',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (response) {
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
    // $('#editSaveDeviceModel').on('click', function () {
    //     const model_id = $('#edit-id').val().trim();
    //     // const payload = { model_id, devices };
    //     const payload = { model_id, device_name };
    //     console.log(payload)

    //     $.ajax({
    //         url: '/connectify-web/controllers/ModelController.php?action=updateDevices',
    //         method: 'POST',
    //         contentType: 'application/json',
    //         data: JSON.stringify(payload),
    //         success: function (response) {
    //             const res = typeof response === 'string' ? JSON.parse(response) : response;
    //             if (res.success) {
    //                 $('#editModelModal').modal('hide');
    //                 alert(res.message);
    //             } else {
    //                 alert(res.message);
    //             }
    //         },
    //         error: function (xhr) {
    //             console.error('Error saving devices:', xhr.responseText);
    //         }
    //     });
    // });
    $('#editSaveDeviceModel').on('click', function () {
        const model_id = $('#edit-id').val().trim();
        const line_area = $('#editDeviceLineArea').val().trim();
        // if (devices.length === 0) {
        //     return alert('Please add at least one device.');
        // }
        const payload = {
                id: model_id, 
                line_area: line_area,
                devices: devices.map(d => ({
                    station_id: d.station_id,
                    device_name: d.device_name
            }))
        };
        console.log(payload)
        $.ajax({
            url: '/connectify-web/controllers/ModelController.php?action=update',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            success: function (response) {
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
            error: function (xhr) {
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

