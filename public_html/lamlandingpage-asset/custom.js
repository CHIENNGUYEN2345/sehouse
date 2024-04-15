$(document).ready(function () {
    $('.colspan-title').click(function () {
        $(this).parents('.colspan').find('.colspan-content').slideToggle(100);
        $(this).parents('.colspan').siblings().find('.colspan-content').slideUp(100);
    });

});