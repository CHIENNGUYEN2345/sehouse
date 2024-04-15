<div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        <?php 
            if(!isset($result)) {
                $field['value'] = date('Y-m-d');
            }
            ?>
        @include(config('core.admin_theme').".form.fields.date_vi", ['field' => $field])
    </div>
</div>