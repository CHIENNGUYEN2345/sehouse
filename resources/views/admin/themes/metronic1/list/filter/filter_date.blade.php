<select name="{{ $name }}" class="form-control kt-input {{ @$field['class'] }}">
    @foreach($field['options'] as $k => $value)
        <option value="{{ $k }}"
                {{ ((isset($_GET[$name]) && $_GET[$name] != '' && @$_GET[$name] == $k)
                || (isset($field['value']) && $field['value'] == $k)) ? 'selected' : '' }}>{{trans($value) }}</option>
    @endforeach
</select>
<div>
    <div style="width: 50%; float: left;">
        <input name="{{ $name }}_from_date" class="form-control kt-input" placeholder="Từ ngày" type="date"
               style="padding: 6px 1px;"
               value="{{ @$_GET[$name.'_from_date'] }}" title="Từ ngày">
    </div>
    <div style="width: 50%; float: left;">
        <input name="{{ $name }}_to_date" class="form-control kt-input" placeholder="Đến ngày" type="date"
               style="padding: 6px 1px;"
               value="{{ @$_GET[$name.'_to_date'] }}" title="Đến ngày">
    </div>
</div>