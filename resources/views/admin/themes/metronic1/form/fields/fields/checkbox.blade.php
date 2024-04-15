<label style="cursor: pointer" for="{{ str_slug(@$field['label'], '-') }}" class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
    <input style="height: 20px; width: 18px; float: left; margin-right: 5px;"
           type="checkbox"
           name="{{ @$field['name'] }}"
           id="{{ str_slug(@$field['label'], '-') }}"
           {!! @$field['inner'] !!}
           value="{{ @$field['value'] }}" {{ (old(@$field['name']) != null || (isset($field['value']) && $field['value'] != 0)) ? 'checked' : '' }}>
    {{ trans(@$field['label']) }}
    <span></span>
</label>