@if($item->{$field['name']} != null)
	<?php 
		$date1=date_create(env('LEAD_MAX_DATE'));
		$date2=date_create(date('Y-m-d', strtotime($item->updated_at)));
		$diff=date_diff($date1,$date2);
    ?>		
	@if($diff->d < env('LEAD_MAX_DATE'))
		<span style="color: red;">{{ $diff->d }} ngày chưa update</span>
	@else(strtotime(date('Y-m-d')) == strtotime($item->{$field['name']}))
		<span style="color: green;">{{ $diff->d }} ngày chưa update</span>
	@endif
@endif