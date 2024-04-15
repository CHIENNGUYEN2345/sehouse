@if(@$item->{$field['name']} == 'Đang quan tâm')
	<span style="background: green; color: #fff;">{{ $item->{$field['name']} }}</span>
@elseif(@$item->{$field['name']} == 'Quan tâm & đã xem dự án')
	<span style="background: #951b00; color: #fff;">{{ $item->{$field['name']} }}</span>
@elseif(@$item->{$field['name']} == 'Quan tâm sâu, muốn mua th')
	<span style="background: #ff2e00; color: #fff;">{{ $item->{$field['name']} }}</span>
@else
{!! $item->{$field['name']} !!}
@endif