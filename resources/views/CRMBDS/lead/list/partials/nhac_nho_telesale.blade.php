
<script type="text/javascript">
	function startTimer(duration) {
		setInterval(function () {
			if (++duration % (3 * 60) == 0) {
				$.ajax({
					url: '/nhac-nho-lau-khong-tuong-tac',
					data: {
						admin_id: '{{ \Auth::guard('admin')->user()->id }}',
						role: '{{ CommonHelper::getRoleName(\Auth::guard('admin')->user()->id, 'name') }}'
					},
					success: function (resp) {
						if (resp.thong_bao == true) {
							alert('Thông báo: Đã lâu chưa có log tương tác mới!');
						} else {
							console.log('ok');
						}
					},
					error: function(resp) {
						console.log('lỗi ajax nhac-nho-lau-khong-tuong-tac!');
					}
				});
			}
		}, 1000);
	}

	startTimer(0);
</script>