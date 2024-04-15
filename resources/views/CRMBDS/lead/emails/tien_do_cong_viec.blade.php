<?php
$admin_ids = [283, 302];
?>


@foreach($admin_ids as $admin_id)
<?php

$lead_chua_log = \App\CRMBDS\Models\Lead::select('id', 'tel', 'name', 'created_at')
					->where('saler_ids', 'like', '%|' . $admin_id . '|%')
					->whereIn('status', ['Đang chăm sóc'])
					->where('contacted_log_last', null)
					->get();

$lead_sap_tha_noi = \App\CRMBDS\Models\Lead::select('id', 'tel', 'name', 'created_at', 'contacted_log_last')
					->where('saler_ids', 'like', '%|' . $admin_id . '|%')
					->whereIn('status', ['Đang chăm sóc'])
					->where('contacted_log_last', '<=', date('Y-m-d', time() - 259200))
					->get();
?>
<h4><strong style="color: red;">{{ @\App\Models\Admin::find($admin_id)->name }}</strong></h4>
<strong>Các đầu mối chưa liên hệ:</strong>
<ul>
	@foreach($lead_chua_log as $v)
		<li><a target="_blank" style="    text-decoration: unset;" href="/admin/lead/edit?code={{ $v->tel }}-{{ date('d-m-Y', strtotime($v->created_at)) }}-{{ $v->id }}">{{ $v->name }} - {{ $v->tel }}</a></li>
	@endforeach
</ul>

<strong>Các đầu mối sắp thả nổi:</strong>
<ul>
	@foreach($lead_sap_tha_noi as $v)
		<?php
		$now = time(); // or your date as well
		$your_date = strtotime($v->contacted_log_last);
		$datediff = $now - $your_date;

		$diff = round($datediff / (60 * 60 * 24));
		?>
		<li><a target="_blank" style="    text-decoration: unset;" href="/admin/lead/edit?code={{ $v->tel }}-{{ date('d-m-Y', strtotime($v->created_at)) }}-{{ $v->id }}">{{ $v->name }} - {{ $v->tel }} 
			<i style="font-size: 12px; color: #000;">({{ $diff }} ngày chưa cập nhật)</i></a></li>
	@endforeach
</ul>
@endforeach