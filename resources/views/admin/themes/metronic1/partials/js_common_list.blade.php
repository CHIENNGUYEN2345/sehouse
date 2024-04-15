<script type="text/javascript">
    function sort(column) {
        if ($('form.form-search .sort-' + column).val() == '' || $('form.form-search .sort-' + column).val() == column + '|asc') {
            $('form.form-search .sort').val('');
            $('form.form-search .sort-' + column).val(column + '|desc');
        } else {
            $('form.form-search .sort').val('');
            $('form.form-search .sort-' + column).val(column + '|asc');
        }
        $('form.form-search').submit();
    }

    function allDelete() {
        if (confirm('Bạn có chắc chắn muốn xóa tất cả?')) {
            window.location.href = "/admin/{{ $module['code'] }}/delete-all";
        }
    }

    function multiDelete() {
        var ids = [];
        $('.ids:checkbox:checked').each(function (i) {
            ids[i] = $(this).val();
        });
        if (ids.length == 0) {
            alert('Bạn chưa chọn bản ghi nào để xóa!');
        } else {
            if (confirm('Bạn có chắc chắn muốn xóa?')) {
                $.ajax({
                    url: '/admin/{{ @$module['code'] }}/multi-delete',
                    type: 'POST',
                    data: {
                        ids: ids
                    },
                    success: function (result) {
                        if (result.status == true) {
                            location.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function () {
                        alert('Có lỗi xảy ra. Vui lòng load lại website và thử lại!');
                    }
                });
            }
        }
    }
    function multiDeleteByFilter() {
        if (confirm('Bạn có chắc chắn muốn xóa tất cả các bản ghi mà bạn đang lọc?')) {
            <?php
            $str = $_SERVER['REQUEST_URI'];
            $str1 = strpos($_SERVER['REQUEST_URI'], '?') === false ? '?delete-by-filter=true' : '&delete-by-filter=true';
            $str .= $str1;
            $str = str_replace('&amp;', '&', $str);
            ?>
            window.location.replace('<?php echo $str;?>');
        }
    }

    function multiPublish() {
        var ids = [];
        $('.ids:checkbox:checked').each(function (i) {
            ids[i] = $(this).val();
        });
        if (ids.length == 0) {
            alert('Bạn chưa chọn bản ghi nào!');
        } else {
            if (confirm('Bạn có chắc chắn muốn kích hoạt?')) {
                $.ajax({
                    url: '/admin/{{ @$module['code'] }}/multi-publish',
                    type: 'POST',
                    data: {
                        ids: ids
                    },
                    success: function (result) {
                        if (result.status == true) {
                            location.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function () {
                        alert('Có lỗi xảy ra. Vui lòng load lại website và thử lại!');
                    }
                });
            }
        }
    }
    function multiDisPublish() {
        var ids = [];
        $('.ids:checkbox:checked').each(function (i) {
            ids[i] = $(this).val();
        });
        if (ids.length == 0) {
            alert('Bạn chưa chọn bản ghi nào!');
        } else {
            if (confirm('Bạn có chắc chắn muốn hủy kích hoạt?')) {
                $.ajax({
                    url: '/admin/{{ @$module['code'] }}/multi-dispublish',
                    type: 'POST',
                    data: {
                        ids: ids
                    },
                    success: function (result) {
                        if (result.status == true) {
                            location.reload();
                        } else {
                            toastr.error(result.msg);
                        }
                    },
                    error: function () {
                        alert('Có lỗi xảy ra. Vui lòng load lại website và thử lại!');
                    }
                });
            }
        }
    }

    $('.checkbox-master').click(function () {
        $('table tbody td input[type=checkbox]').trigger('click');
    });

    $('input[name=quick_search]').keyup(function(e){
        $('#quick_search_hidden').val($(this).val());
        if(e.keyCode == 13)
        {
            $('form#form-search').submit();
            // window.location.href = "?limit=20&quick_search=" + $(this).val();
        }
    });
</script>