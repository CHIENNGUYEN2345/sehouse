<select name="{{ $name }}" class="form-control kt-input {{ @$field['class'] }}">
    @php
        $model = new $field['model'];
        if(isset($field['where']))
            $model = $model->whereRaw($field['where']);
        $data = $model->orderBy($field['display_field'], 'asc')->pluck('name', 'id');
    @endphp
    <option value="">{{trans('admin.choose')}} {{ @$field['label'] }}</option>
    @foreach ($data as $k => $v)
        <option value="{{ $k }}" {{ ((isset($_GET[$name]) && @$_GET[$name] == $k) || @$field['value'] ==  @$k) ? 'selected' : '' }}>{{ $v }}</option>
    @endforeach
</select>