@if(@$item->{$field['name']} == 'Đang tìm hiểu')
	<span style="background: green; color: #fff;">{{ $item->{$field['name']} }}</span>
@elseif(@$item->{$field['name']} == 'Quan tâm cao')
	<span style="background: #951b00; color: #fff;">{{ $item->{$field['name']} }}</span>
@elseif(@$item->{$field['name']} == 'Đang chốt HĐ')
	<span style="background: #ff2e00; color: #fff;">{{ $item->{$field['name']} }}</span>
@else
{!! $item->{$field['name']} !!}
@endif