<a href="{{ $item->{$field['name']} }}" target="_blank">
    @if(isset($field['inner']))
        {!! $field['inner'] !!}
    @else
        {!! $item->{$field['name']} !!}
    @endif
</a>