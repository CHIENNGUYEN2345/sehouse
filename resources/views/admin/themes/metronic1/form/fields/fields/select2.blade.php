<?php
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
    <option value="">{{trans('admin.choose')}} {{ $field['label'] }}</option>
    @foreach ($field['options'] as $k => $v)
        <option value='{{ $k }}' {{ in_array($k, $value) && $k != '' ? 'selected':'' }}>{{ $v }}</option>
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