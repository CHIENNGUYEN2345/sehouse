@if(isset($item->{$field['name']}))
    @if($item->{$field['name']} == 'Đang bán')
        <span style="color: blue;">{{ $item->{$field['name']} }}</span>
    @elseif($item->{$field['name']} == 'Đã bán')
        <span style="color: red;">{{ $item->{$field['name']} }}</span>
    @else
        <span style="color: black;">{{ $item->{$field['name']} }}</span>
    @endif
@endif