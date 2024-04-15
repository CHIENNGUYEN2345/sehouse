@if(\App\Http\Helpers\CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name') != 'customer')
    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        @include(config('core.admin_theme').".form.fields.select2_ajax_model", ['field' => $field])
    </div>
@endif