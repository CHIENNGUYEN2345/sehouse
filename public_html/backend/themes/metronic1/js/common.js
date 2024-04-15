function loading() {
    if ($('body').find('#loading').length == 0) {
        $('body').append('<div id="loading" style="width: 100%;position: fixed;height: 100%;z-index: 999999;top: 0;text-align: center;background-color: rgba(0, 0, 0, 0.3);"><img style="margin-top: 20%;" src="/images_core/icons/loading.gif"></div>');
    } else {
        $('#loading').show();
    }
}

function stopLoading() {
    $('#loading').hide();
}

$('.board-header-btn, .admins-more').mouseover(function () {
    $('.admins-more').show();
});
$('.board-header-btn, .admins-more').mouseout(function () {
    $('.admins-more').hide();
});

$('body').on('click', '.select-datetimepicker', function () {
    $(this).parent().find('input').focus();
});
