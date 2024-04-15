@if($item->{$field['name']} != null)
	<div  title="Quá {{ env('LEAD_MAX_DATE') }} ngày không update, hệ thống tự động chuyển đầu mối cho sale khác">
		{!! date('d/m/Y', strtotime($item->{$field['name']})) !!}<br>
		<?php 	
			$date1=date_create(date('Y-m-d'));
			$date2=date_create(date('Y-m-d', strtotime($item->updated_at)));
			$diff=date_diff($date1,$date2);
	    ?>		
		@if($diff->d >= (int) env('LEAD_MAX_DATE') - 2)
			<span style="color: red; font-style: italic;
	    font-size: 11px;">{{ $diff->d }} ngày chưa update</span>
		@else(strtotime(date('Y-m-d')) == strtotime($item->{$field['name']}))
			<span style="font-style: italic;
	    font-size: 11px;">{{ $diff->d }} ngày chưa update</span>
		@endif
	</div>
@endif