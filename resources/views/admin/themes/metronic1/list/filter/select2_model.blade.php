<select name="{{ $name }}" class="form-control select2-{{ $name }} {{ @$field['class'] }}">
    @php
        $model = new $field['model'];
        if(isset($field['where']))
            $model = $model->whereRaw($field['where']);

        $display_field = isset($field['display_field']) ? $field['display_field'] : 'name';
        $data = $model->orderBy($display_field, 'asc')->pluck($display_field, 'id');
    @endphp
    <option value="">{{trans('admin.choose')}} {{ trans(@$field['label']) }}</option>
    @foreach ($data as $k => $v)
        <option value="{{ $k }}" {{ ((isset($_GET[$name]) && @$_GET[$name] == $k) || @$field['value'] ==  @$k) ? 'selected' : '' }}>{{ $v }}</option>
    @endforeach
</select>
<script>
    $(document).ready(function () {
        $('.select2-{{ $name }}').select2();
    });
</script>