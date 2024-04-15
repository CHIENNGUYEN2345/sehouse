<?php
$setting = \App\Models\Setting::where('name', @$name)->where('type', @$type)->first();
?>

<form class="form-setting">
    <label>{{ trans(@$label) }}</label>
    <textarea id="profile" name="profile" rows="5" class="form-control " style="max-width: 500px;">{!! $setting->value !!}</textarea>
    <button type="button" class="btn save-setting btn-brand">Lưu</button>
</form>
<script>
    $('.save-setting').click(function () {
        var value = $(this).parents('form').find('textarea').val();
        $.ajax({
            url: '/admin/setting/ajax-update',
            type: 'POST',
            data: {
                name: '{{ @$name }}',
                type: '{{ @$type }}',
                value: value
            },
            success: function(resp) {
                alert(resp.msg + '. Vui lòng load lại trang');
            },
            error: function() {
                alert('Có lỗi xảy ra, vui lòng load lại trang và thử lại!');
            }
        });
    });
</script>