// $(document).on('click', '.deleteimage', function () {          // Delete
//     $(this).click(function () {
//         $('.div0-' + $(this).attr('name')).find('.wrap-thumbnail').html('');
//         $('.thumb .div1-' + $(this).attr('name')).css('display', 'none');
//         $('.thumb .div2-' + $(this).attr('name')).css('display', 'block');
//         $('#' + $(this).attr('name')).val('');
//     });
// });

function BrowseFile(obj) {
    CKFinder.modal({
        language: 'vi',
        chooseFiles: true,
        width: 800,
        height: 600,
        onInit: function (finder) {
            finder.on('files:choose', function (evt) {
                var file = evt.data.files.first();
                var url_image = file.getUrl();
                changeFile(obj, url_image);
            });
            finder.on('file:choose:resizedImage', function (evt) {
                var url_image = evt.data.resizedUrl;
                changeFile(obj, url_image);
            });
        }
    });
}

function changeFile(obj, url_image) {
    var current_image = $("#form-group-" + obj).find('#' + obj).val();
    if (url_image != current_image) {
        $("#form-group-" + obj).find('#' + obj).val(url_image);
        $("#form-group-" + obj).find('.div0-' + obj).find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(' + url_image + ');');
        $("#form-group-" + obj).find('.div1-' + obj).find('.kt-avatar').addClass('kt-avatar--changed');
    }
}

//Multi image

function BrowseMultiFile(obj) {
    CKFinder.modal({
        language: 'vi',
        chooseFiles: true,
        width: 800,
        height: 600,
        onInit: function (finder) {
            finder.on('files:choose', function (evt) {
                var file = evt.data.files.first();
                var url_image = file.getUrl();
                changeMultiFile(obj, url_image);
            });
            finder.on('file:choose:resizedImage', function (evt) {
                var url_image = evt.data.resizedUrl;
                changeMultiFile(obj, url_image);
            });
        }
    });
}

function changeMultiFile(obj, url_image) {
    var current_image = $("#" + obj).val();
    if (url_image != current_image) {
        $('#' + obj).val(url_image);
        $(".item-" + obj).find('.wrap-thumbnail .kt-avatar__holder').attr('style', 'background-image: url(' + url_image + ');');
        $(".item-" + obj).find('.kt-avatar').addClass('kt-avatar--changed');
    }
}
