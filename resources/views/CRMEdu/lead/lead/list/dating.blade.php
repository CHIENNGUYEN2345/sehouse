@if($item->{$field['name']} != null)
	<?php 
		$date1=date_create(@$result->dating);
		$date2=date_create(date('Y-m-d', strtotime('+3 days')));
		$diff=date_diff($date1,$date2);
    ?>		
	@if(strtotime(date('Y-m-d')) > strtotime($item->{$field['name']}))
		<span style="color: red;">Trễ {{ $diff->d }} ngày</span>
	@elseif(strtotime(date('Y-m-d')) == strtotime($item->{$field['name']}))
		<span style="color: green;">Đến ngày TT</span>
	@else
		{!! date('d/m/Y', strtotime($item->{$field['name']})) !!}
	@endif
@endif