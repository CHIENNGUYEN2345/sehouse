<div  title="Quá {{ env('LEAD_MAX_DATE') }} ngày không update, hệ thống tự động chuyển đầu mối cho sale khác">
	
    @if($item->contacted_log_last != null)
    	{!! date('d/m', strtotime($item->contacted_log_last)) !!}<br>
	    <?php 	
	    $now = time(); // or your date as well
$your_date = strtotime($item->contacted_log_last);
$datediff = $now - $your_date;

$diff = round($datediff / (60 * 60 * 24));

	    ?>	
	    @if($diff == 0)
			<span style=" font-style: italic;
				    font-size: 11px;">mới cập nhật</span>
		@elseif($diff >= (int) env('LEAD_MAX_DATE') - 2)
			<span style="color: red; font-style: italic;
	    font-size: 11px;">{{ $diff }} ngày chưa cập nhật</span>
		@else(strtotime(date('Y-m-d')) == strtotime($item->{$field['name']}))
			<span style="font-style: italic;
	    font-size: 11px;">{{ $diff }} ngày chưa cập nhật</span>
		@endif
	@else
			<span style=" font-style: italic;
				    font-size: 11px;">-</span>
	@endif
</div>