<script>
    var count_clicks = 0;
    $(document).ready(function () {
        $('body').on('change', '#domain_id', function () {
            getDomain();
        })

        function getDomain() {
            @if($old)
                if (count_clicks == 0) {
                    $('#links').parent().find('.form-text.text-muted').text('Đường dẫn cũ : ' + $('#links').val());
                    count_clicks++;
                }
            @endif
            let domain = $('#domain_id option:selected').text();
            $('#links').val(domain);

        }
    })
</script>