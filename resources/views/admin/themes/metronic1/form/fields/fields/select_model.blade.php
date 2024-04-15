@php
    $model = new $field['model'];
    if(isset($field['where']))
        $model = $model->whereRaw($field['where']);
    $data = $model->orderBy($field['display_field'], 'asc')->get();
@endphp
<?php
if (isset($field['multiple'])) {
    $value = old($field['name']) != null ? old($field['name']) : explode('|', @$field['value']);
} else {
    $value[] = old($field['name']) != null ? old($field['name']) : @$field['value'];
}
?>
<select class="form-control {{ $field['class'] or '' }}" id="{{ $field['name'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        name="{{ $field['name'] }}" {!! @$field['inner'] !!}>
    <option value="">{{trans('admin.choose')}} {{ $field['label'] }}</option>
    @foreach ($data as $v)
        <option value='{{ $v->id }}' {{ in_array($v->id, $value) ? 'selected' : '' }}>{{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}</option>
    @endforeach
</select>