<?php
if (isset($field['multiple'])) {
    $data = old($field['name']) != null ? old($field['name']) : explode('|', @$field['value']);
} else {
    $data[] = old($field['name']) != null ? old($field['name']) : @$field['value'];
}
@$field['value'] = explode('|', @$field['value']);
?>
@foreach ($field['options'] as $value => $name)
    <label style="cursor: pointer;     margin-right: 20px;" for="checkbox_multiple-{{ $field['name'] }}-{{ @$value }}"
           class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
        <input style="height: 20px; width: 18px; float: left; margin-right: 5px;"
               type="checkbox"
               name="{{ @$field['name'] }}[]"
               id="checkbox_multiple-{{ $field['name'] }}-{{ @$value }}"
               {!! @$field['inner'] !!}
               value="{{ @$value }}" {{ (in_array(@$value, $field['value'])) ? 'checked' : '' }}>
        {{ trans($name) }}
        <span></span>
    </label>
@endforeach