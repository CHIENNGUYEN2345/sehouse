<?php
$model = new $field['model'];
if (isset($field['where'])) {
    $model = $model->whereRaw($field['where']);
}
if (isset($field['where_attr']) && isset($result)) {
    $model = $model->where($field['where_attr'], $result->{$field['where_attr']});
}
$data = $model->orderBy($field['display_field'], 'asc')->get();
$value = '';
if (old($field['name']) != null) $value = old($field['name']);
if (isset($field['value'])) $value = $field['value'];
?>
<div class="kt-radio-list mt-3">
    @foreach ($data as $v)
        <label class="kt-radio">
            <input type="radio" name="{{ $field['name'] }}"
                   {{ $v->id == $value ? 'checked':'' }} value="{{ $v->id }}"> {{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}
            <span></span>
        </label>
    @endforeach
</div>