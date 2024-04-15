<div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        <div class="col-md-6">

        @include(config('core.admin_theme').".form.fields.date_vi", ['field' => $field])
        </div>
<span class="text-danger">Quá {{ env('LEAD_MAX_DATE') }} ngày không có cập nhật tương tác thì hệ thống tự động thu hồi đầu mối và chuyển cho sale khác</span>
    </div>
</div>