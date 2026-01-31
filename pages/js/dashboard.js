$(document).ready(function () {
    $('#saveSetting').click(function () {
        // $(document).on('click', '#saveSetting', function() {
        const password = $('#settingPassword').val();
        const passwordConfirm = $('#settingPasswordConfirm').val();

        if (password !== passwordConfirm) {
            $('#message-edit-container').html(
                `<div class="alert alert-danger">Passwords do not match!</div>`
            );
            return;
        }

        var payload = {
            name: CURRENT_USER_NAME,
            work_id: CURRENT_USER_WORK_ID,
            department_id: CURRENT_USER_DEPT_ID,
            role_id: CURRENT_USER_ROLE_ID,
            password: $('#settingPasswordConfirm').val()
        };
        // console.log(payload);
        // <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        $.ajax({
            url: '/connectify-web/controllers/UserController.php',
            type: 'PUT',
            data: JSON.stringify(payload),
            contentType: 'application/json',
            processData: false,
            success: function (response) {
                if (response.success) {
                    $('#message-edit-container').html(
                        `<div class="alert alert-success alert-dismissible fade show" role="alert">
                            Password edited successfully
                        </div>`
                    );
                    setTimeout(() => {
                        $('#settingForm')[0].reset();
                        $('#editSettingModal').modal('hide');
                        $('.alert').alert('close');
                    }, 1500);
                } else {
                    $('#message-edit-container').html(
                        `<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            ${response.message}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>`
                    );
                }
            },
            error: function (xhr, status, error) {
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

    $('#profileModal').on('show.bs.modal', function () {
        $('#profileName').text(CURRENT_USER_NAME);
        $('#profileWorkId').text(CURRENT_USER_WORK_ID);
        $('#profileDept').text(CURRENT_USER_DEPT);
        $('#profileRole').text(CURRENT_USER_ROLE);
    });

    $('#cancelSetting').click(function () {
        $('#userForm')[0].reset();
        $('#message-container').html('');
    });
});
