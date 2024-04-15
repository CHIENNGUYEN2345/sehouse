@if(\Modules\LandingPage\Http\Helpers\LandingpageHelper::getRoleType(\Auth::guard('admin')->user()->id) == 'admin')
    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        @include(config('core.admin_theme').".form.fields.select2_ajax_model_popup", ['field' => $field])
    </div>
@endif