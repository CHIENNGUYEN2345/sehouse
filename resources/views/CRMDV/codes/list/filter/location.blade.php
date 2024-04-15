    @if(@$field['province'] !== false)
        @php
            $fd = ['name' => 'province_id', 'type' => 'select2_ajax_model', 'label' => 'Tỉnh / thành', 'model' => \App\Models\Province::class,
            'object' => 'province', 'display_field' => 'name', 'group_class' => 'col-md-4'];
            $fd['value'] = @$result->{$fd['name']};
        @endphp
        <div class="col-sm-6 col-lg-6 kt-margin-b-10-tablet-and-mobile list-filter-item">
            <div class="form-group-div form-group {{ @$fd['group_class'] }} col-md-12  p-0"
                 id="form-group-{{ $fd['name'] }}">
                <label for="{{ $fd['name'] }}">{{ @$fd['label'] }} @if(strpos(@$fd['class'], 'require') !== false)
                        <span class="color_btd">*</span>@endif</label>
                @include(config('core.admin_theme').".form.fields." . $fd['type'], ['field' => $fd])
                <span class="text-danger">{{ $errors->first('province_id') }}</span>
            </div>
        </div>
    @endif

    @if(@$field['district'] !== false)
        @php
            $fd = ['name' => 'district_id', 'type' => 'select2_ajax_model', 'label' => 'Quận / Huyện', 'model' => \App\Models\District::class,
            'object' => 'district', 'display_field' => 'name', 'group_class' => 'col-md-4'];
            $fd['value'] = @$result->{$fd['name']};
        @endphp
        <div class="col-sm-6 col-lg-6 kt-margin-b-10-tablet-and-mobile list-filter-item">
            <div class="form-group-div form-group {{ @$fd['group_class'] }} col-md-12  p-0"
                 id="form-group-{{ $fd['name'] }}">
                <label for="{{ $fd['name'] }}">{{ @$fd['label'] }} @if(strpos(@$fd['class'], 'require') !== false)
                        <span class="color_btd">*</span>@endif</label>
                <div id="{{ $fd['name'] }}">
                    @include(config('core.admin_theme').".form.fields." . $fd['type'], ['field' => $fd])
                </div>
                <span class="text-danger">{{ $errors->first('district_id') }}</span>
            </div>
        </div>
    @endif



<script>
    $(document).ready(function () {
        $('body').on('change', 'select[name=province_id]', function () {
            var province_id = $(this).val();
            $.ajax({
                url : '/admin/location/districts/get-data',
                type: 'GET',
                data: {
                    province_id : province_id,
                },
                success: function (resp) {
                    var data = resp.data;
                    var html = '<select name="district_id" class="form-control"><option value="">Chọn quận / huyện</option>';
                    Object.keys(data).map(function(key) {
                        html += '<option value="'+key+'">'+data[key]+'</option>';
                    });
                    html += '</select>';
                    $('#district_id').html(html);
                    changeWard();
                },
                error: function () {
                    alert('Có lỗi xảy ra! Vui lòng load lại trang và thử lại');
                }
            });
        });

        function changeWard() {
            var district_id = $('select[name=district_id]').val();
            $.ajax({
                url : '/admin/location/wards/get-data',
                type: 'GET',
                data: {
                    district_id : district_id,
                },
                success: function (resp) {
                    var data = resp.data;
                    var html = '<select name="ward_id" class="form-control"><option value="">Chọn phường / xã</option>';
                    Object.keys(data).map(function(key) {
                        html += '<option value="'+key+'">'+data[key]+'</option>';
                    });
                    html += '</select>';
                    $('#ward_id').html(html);
                },
                error: function () {
                    alert('Có lỗi xảy ra! Vui lòng load lại trang và thử lại');
                }
            });
        }

        $('body').on('change', 'select[name=district_id]', function () {
            changeWard();
        });
    });
</script>