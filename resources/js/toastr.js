var $ = require('jquery');
var toastr = require('toastr');

$(function() {
    toastr.options = {
        "closeButton": true,
        "positionClass": "toast-top-center",
        "timeOut": "5000",
    };
    if($('.msg_success').length) {
        toastr.success(msg_success);
    }
    else if ($('.msg_warning').length) {
        toastr.warning(msg_warning);
    }
    else if ($('.msg_error').length) {
        toastr.error(msg_error);
    }
});