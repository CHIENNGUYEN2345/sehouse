@php
    $model = new \App\Models\Attribute();
    if(isset($field['where']))
        $model = $model->whereRaw($field['where']);
    $data = $model->orderBy('key', 'asc')->groupBy('key')->get();
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
        name="{{ $field['name'] }}">
    <option value="">{{trans('admin.choose')}} {{ $field['label'] }}</option>
    @foreach ($data as $v)
        <option value='{{ $v->key }}' {{ in_array($v->key, $value) ? 'selected' : '' }}>{{ $v->{$field['display_field']} }}</option>
    @endforeach
</select>