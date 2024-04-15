<div>
    @if(@$item->bill_progress->reminder_customer != null)
    	{!! date('d/m', strtotime($item->bill_progress->reminder_customer)) !!}<br>
	    <?php 	
	    $now = time(); // or your date as well
$your_date = strtotime($item->bill_progress->reminder_customer);
$datediff = $now - $your_date;

$diff = round($datediff / (60 * 60 * 24));

	    ?>	
	    @if($diff == 0)
			<span style=" font-style: italic;
				    font-size: 11px; color: green;">Đến deadline</span>
		@elseif(strtotime(date('Y-m-d')) > strtotime(@$item->bill_progress->reminder_customer))
				<span style="color: red; font-style: italic;
		    font-size: 11px;">trễ {{ $diff }} ngày</span>
		@else
				<span style="font-style: italic;
		    font-size: 11px;">{{ $diff }} ngày</span>
		@endif
	@else
			<span style=" font-style: italic;
				    font-size: 11px;">-</span>
	@endif
</div>