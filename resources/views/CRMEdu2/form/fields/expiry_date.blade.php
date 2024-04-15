<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <?php
        if (!isset($result)) {
            $field['value'] = date('Y-m-d', strtotime('+1 year'));
        }
    ?>
    @include(config('core.admin_theme').".form.fields.date", ['field' => $field])
</div>
