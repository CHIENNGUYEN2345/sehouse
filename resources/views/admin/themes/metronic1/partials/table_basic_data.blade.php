<script type="text/javascript">
	$.ajax({
		url: '{{ $field['url'] }}',
		type: 'GET',
		data: {
			where: '{{ $field['where'] }}'
		},
		success: function (html) {
			$('#table_basic_data_{{ $field['id'] }}').html(html);
		},
		error: function() {
			console.log('Lỗi ajax table-basic-data');
		}
	});
</script>
<div id="table_basic_data_{{ $field['id'] }}"></div>