<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    @include(config('core.admin_theme').".form.fields.select2_ajax_model", ['field' => $field])
    <span class="form-text text-muted">{!! @$field['des'] !!}</span>
    <span class="text-danger">{{ $errors->first($field['name']) }}</span>
    <span class="form-text text-muted"><a href="javascript:;" class="btn-popup-create-category_product">Tạo mới danh mục</a></span>
</div>
