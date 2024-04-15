<select name="{{ $name }}" class="form-control kt-input {{ @$field['class'] }}">
    @foreach($field['options'] as $k => $value)
        <option value="{{ $k }}" {{ ((isset($_GET[$name]) && $_GET[$name] != '' && @$_GET[$name] == $k)
        || (isset($field['value']) && $field['value'] == $k)) ? 'selected' : '' }}>{{trans($value) }}</option>
    @endforeach
</select>