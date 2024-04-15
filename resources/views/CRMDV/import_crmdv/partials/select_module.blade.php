<label for="{{ $field['name'] }}">{{ trans(@$field['label']) }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>@endif</label>
<div class="col-xs-12">
    <?php
    if (isset($field['multiple'])) {
        $data = old($field['name']) != null ? old($field['name']) : explode('|', @$field['value']);
    } else {
        $data[] = old($field['name']) != null ? old($field['name']) : @$field['value'];
    }
    ?>
    <select class="form-control {{ $field['class'] or '' }}" id="{{ $field['name'] }}" {!! @$field['inner'] !!}
    {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
    name="{{ $field['name'] }}@if(isset($field['multiple'])){{ '[]' }}@endif"
            @if(isset($field['multiple'])) multiple @endif>
        @foreach ($field['options'] as $value => $name)
            <option value="{{ $value }}" {{ in_array($value, $data) ? 'selected' : '' }}>{{ trans($name) }}</option>
        @endforeach
        @if(isset($_GET['table']))
            <option value="{{ $_GET['table'] }}" selected>{{ isset($_GET['table_label']) ? $_GET['table_label'] : $_GET['table'] }}</option>
        @endif
    </select>
</div>