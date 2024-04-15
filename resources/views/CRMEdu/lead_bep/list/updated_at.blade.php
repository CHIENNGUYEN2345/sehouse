@if($item->{$field['name']} != null)
	<div  title="Quá {{ env('LEAD_MAX_DATE') }} ngày không update, hệ thống tự động chuyển đầu mối cho sale khác">
		{!! date('d/m/Y', strtotime($item->{$field['name']})) !!}<br>
		<?php 	
			$date1=date_create(date('Y-m-d'));
			$date2=date_create(date('Y-m-d', strtotime($item->updated_at)));
			$diff=date_diff($date1,$date2);
	    ?>		
	    @if($diff->d == 0)
			<span style=" font-style: italic;
				    font-size: 11px;">mới cập nhật</span>
		@elseif($diff->d >= (int) env('LEAD_MAX_DATE') - 2)
			<span style="color: red; font-style: italic;
	    font-size: 11px;">{{ $diff->d }} ngày chưa cập nhật</span>
		@else(strtotime(date('Y-m-d')) == strtotime($item->{$field['name']}))
			<span style="font-style: italic;
	    font-size: 11px;">{{ $diff->d }} ngày chưa cập nhật</span>
		@endif
	</div>
@endif