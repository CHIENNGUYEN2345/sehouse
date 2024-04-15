@if($item->{$field['name']} != null)
	<?php 
		$date1=date_create(@$item->dating);
		$date2=date_create(date('Y-m-d'));
		$diff=date_diff($date1,$date2);
    ?>		
	@if(strtotime(date('Y-m-d')) > strtotime($item->{$field['name']}))
		<span style="color: red; font-weight: bold;">Trễ {{ $diff->format('%a') }} ngày</span>
	@elseif(strtotime(date('Y-m-d')) == strtotime($item->{$field['name']}))
		<span style="color: green; font-weight: bold;">Đến ngày TT</span>
	@else
		<i style="font-size: 11px;">{!! date('d/m/Y', strtotime($item->{$field['name']})) !!}</i>
	@endif
	<input type="date" name="dating_change" class="td-field-{{ $item->id }}" style="width: 55px;
    border: 0;
    position: absolute;
    right: 0;
    bottom: 7px;" value="{{ date('Y-m-d', strtotime($item->dating)) }}">
@endif
<script type="text/javascript">
	$('.td-field-{{ $item->id }}').change(function() {
		var dating = $(this).val();
		console.log(dating, '{{ $item->id }}');

		$.ajax({
			url: '/admin/lead/ajax-update',
			type: 'POST',
			data: {
				data: {
					dating: dating
				},
				id: '{{ $item->id }}'
			},
			success: function() {
				location.reload();
			},
			error: function() {
				console.log('Có lỗi xảy ra, vui lòng load lại trang và thử lại!');
			}
		});
	});

	
</script>