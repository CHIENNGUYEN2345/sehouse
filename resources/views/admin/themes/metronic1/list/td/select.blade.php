@if(isset($field['options'][$item->{$field['name']}]))
    {{ trans(@$field['options'][$item->{$field['name']}]) }}
@else
    {{ $item->{$field['name']} }}
@endif