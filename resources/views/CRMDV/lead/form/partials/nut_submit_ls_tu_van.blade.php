<button type="button" class="log_submit">Lưu lại</button>
<script type="text/javascript">
    {{--log lịch sử tư vấn--}}
    $('.log_action .log_submit').click(function() {
        if ($('textarea[name=log_note]').val() == '') {
            alert('Không được để trống Nội dung tư vấn');
        } else {
            $.ajax({
                url: '/admin/lead/lead-contacted-log',
                type: 'POST',
                data: {
                    title: $('input[name=log_name]').val(),
                    note: $('textarea[name=log_note]').val(),
                    lead_id: '{{ @$result->id }}',
                    type: 'lead',
                },
                success: function() {
                    location.reload();
                    // window.location.href = "/admin/lead";
                },
                error: function() {
                    alert('Có lỗi xảy ra. Vui lòng load lại trang và thử lại!');
                }
            });
        }
    });
</script>