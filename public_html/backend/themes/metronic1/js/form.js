$('.save_option').click(function () {
    var action = $(this).data('action');
    $('input[name=return_direct]').val(action);
    $('form.kt-container').find('button[type=submit]').click();
});
/*
function collapsePortlet(object) {
    object.parents('.kt-portlet').find('.kt-form').slideToggle();
}*/

$(document).ready(function () {
    $('form').submit(function () {
        loading();
    });
});