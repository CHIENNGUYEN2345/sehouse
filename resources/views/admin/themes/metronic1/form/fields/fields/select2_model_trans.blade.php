<?php
$model = new $field['model'];
if (isset($field['where'])) {
    $model = $model->whereRaw($field['where']);
}
if (isset($field['where_attr']) && isset($result)) {
    $model = $model->where($field['where_attr'], $result->{$field['where_attr']});
}
$data = $model->orderBy($field['display_field'] . '_' . $language, 'asc')->get();
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
<select class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}" id="{{ $field['name'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }}>
    <option value="">{{trans('admin.choose')}} {{ trans($field['label']) }}</option>
    @foreach ($data as $v)
        <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected':'' }}>{{ $v->{$field['display_field'] . '_' . $language} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}</option>
    @endforeach
</select>
<script>
    $(document).ready(function () {
        $('.select2-{{ $field['name'] }}').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
        });
    });
</script>