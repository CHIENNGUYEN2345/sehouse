<script type="text/javascript">
	$.ajax({
		url: '/admin/bill_progress_history/ajax-lich-su-trang-thai',
		type: 'GET',
		data: {
			bill_id: '{{ @$result->id }}'
		},
		success: function (html) {
			$('#table_basic_data_lstt').html(html);
		},
		error: function() {
			console.log('Lỗi ajax table-basic-data');
		}
	});
</script>
<div id="table_basic_data_lstt"></div>