// $('#createReportModal, #abnormalFilterModal').on('shown.bs.modal', function () {
//     $('#modelSelect, #stationSelect, #deviceSelect, #shift, #errorCodeSelect, #filterDept, #filterModel, #filterStation, #filterDevice').select2({
//         dropdownParent: $('#createReportModal'),
//         width: '100%'
//     });
// });
function initSelectAbnormal(modal) {
    $(modal).find('#modelSelect, #stationSelect, #deviceSelect, #shift, #errorCodeSelect, #filterDept, #filterModel, #filterStation, #filterDevice')
        .select2({
            dropdownParent: $(modal),
            width: '100%'
        });
}
$('#createReportModal, #abnormalFilterModal').on('shown.bs.modal', function () {
    initSelectAbnormal(this);
});

function initSelectDaily(modal) {
    $(modal).find('#modelSelect, #statusUphSelect, #filterDept, #filterModel, #editstatusUphSelect')
        .select2({
            dropdownParent: $(modal),
            width: '100%'
        });
}
$('#createTargetReportModal, #dailyFilterModal, #editTargetReportModal').on('shown.bs.modal', function () {
    initSelectDaily(this);
});

function initSelectSec(modal) {
    $(modal).find('#filterModel, #modelSelect, #shift')
        .select2({
            dropdownParent: $(modal),
            width: '100%'
        });
}
$('#sec1FilterModal, #createSec1Modal, #sec2FilterModal, #createSec2Modal, #sec3FilterModal, #createSec3Modal').on('shown.bs.modal', function () {
    initSelectSec(this);
});

function initSelectModel(modal) {
    $(modal).find('#userOwner, #userMembers, #editUserOwner, #editUserMembers, #editDeviceStation')
        .select2({
            dropdownParent: $(modal),
            width: '100%'
        });
}
$('#createModelModal, #editModelModal').on('shown.bs.modal', function () {
    initSelectModel(this);
});

function initSelectManagement(modal) {
    $(modal).find('#location_id, #editlocation_id, #department_id, #role_id, #editdepartment_id, #editrole_id')
        .select2({
            dropdownParent: $(modal),
            width: '100%'
        });
}
$('#createServerModal, #editServerModal, #createUserModal, #editUserModal').on('shown.bs.modal', function () {
    initSelectManagement(this);
});