<?php
$settingss =\App\Models\Setting::where('name','list_font')->where('type','common_tab')->first();
$list_fonts =  explode("\r\n",@$settingss->value);

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
    @foreach ($list_fonts as $value => $name)
        <option value='{{ $value }}' {{ in_array($value, $data) ? 'selected' : '' }}>{{ trans($name) }}</option>
    @endforeach
</select>