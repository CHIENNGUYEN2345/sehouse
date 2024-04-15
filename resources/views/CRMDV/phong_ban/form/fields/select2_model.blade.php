<?php
$model = new $field['model'];
$ktv = \App\Models\RoleAdmin::where('role_id', '=', 2)->pluck('admin_id')->toArray();
$model = $model->whereIn('id', $ktv);
if (isset($field['where'])) {
    $model = $model->whereRaw($field['where']);
}
$model = $model->where($field['display_field'], '!=', '');
if (isset($field['where_attr']) && isset($result)) {
    $model = $model->where($field['where_attr'], $result->{$field['where_attr']});
}

// Thêm điều kiện kiểm tra cột 'status' = 0
$model = $model->where('status', '!=', 0);

$data = $model->orderBy($field['display_field'], 'asc')->get();
$value = [];
if (isset($field['multiple']) && isset($result)) {
    if (is_array($result->{$field['name']}) || is_object($result->{$field['name']})) {
        foreach ($result->{$field['name']} as $item) {
            $value[] = $item->id;
        }
    } elseif (is_string($result->{$field['name']})) {
        $value = explode('|', $result->{$field['name']});
    }
} else {
    if (old($field['name']) != null) $value[] = old($field['name']);
    if (isset($field['value'])) $value[] = $field['value'];
}
?>
<label>{{  $field['label'] }}</label>
<select class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}" id="{{ $field['name'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }}>

    <option value="">{{trans('admin.choose')}} {{ $field['label'] }}</option>
    @foreach($data as $item)
        <option value='{{ @$item->id }}' {{ in_array(@$item->id, $value) ? 'selected':'' }}>{{ @$item->{$field['display_field']} }}</option>
    @endforeach
</select>
<script>
    $(document).ready(function () {
        $('.select2-{{ $field['name'] }}').select2();
    });
</script>
