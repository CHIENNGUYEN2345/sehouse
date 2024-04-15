<?php
if (isset($field['multiple'])) {
    $data = old($field['name']) != null ? old($field['name']) : explode('|', @$field['value']);
} else {
    $data[] = old($field['name']) != null ? old($field['name']) : @$field['value'];
}

$model = new $field['model'];
$options = $model->groupBy($field['name'])->pluck($field['name'])->toArray();
?>
<select class="form-control {{ $field['class'] or '' }}" id="{{ $field['name'] }}" {!! @$field['inner'] !!}
{{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
name="{{ $field['name'] }}@if(isset($field['multiple'])){{ '[]' }}@endif"
        @if(isset($field['multiple'])) multiple @endif>
    @foreach ($options as $value)
        <option value='{{ $value }}' {{ in_array($value, $data) ? 'selected' : '' }}>{{ @$field['options'][$value] }}</option>
    @endforeach
</select>