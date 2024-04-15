$(document).ready(function () {
    $('body').on('click', 'table .publish', function () {
        loading();
        var img = $(this);
        var id = img.data('id');
        var column = img.data('column');
        let url = img.data('url');
        if (url == '') {
            console.log(url, 1);
            url = '/' + location.pathname.substring(1) + '/publish';
        } else{
            console.log(url, 2);
        }

        $.ajax({
            url: url,
            type: 'GET',
            dataType: 'json',
            data: {
                id: id,
                column: column
            },
            success: function (result) {
                stopLoading();
                console.log(result)
                if (result.status) {
                    if (result.published) {
                        img.parent().html('<span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill publish" data-id="' + id + '" \n' +
                            '              style="cursor:pointer;" data-url="' + url + '" data-column="' + column + '">Kích hoạt</span>');
                    } else {
                        img.parent().html('<span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-id="' + id + '" \n' +
                            '              style="cursor:pointer;" data-url="' + url + '" data-column="' + column + '">Tạm dừng</span>');
                    }
                    if (result.msg){
                        toastr.success(result.msg);
                    }
                } else {
                    toastr.error(result.msg);
                }
            },
            error: function () {
                stopLoading();
                $('#something-went-wrong').modal('show');
            }
        });
    });
});

