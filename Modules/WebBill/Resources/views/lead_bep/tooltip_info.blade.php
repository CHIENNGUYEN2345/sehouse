<div class="log_logs">
	<!-- @if (\Auth::guard('admin')->user()->super_admin == 1)
    <?php 
        $sales = \App\Models\Admin::select('name', 'tel')->whereIn('id', explode('|', $lead->saler_ids))->get();
    ?>
<span>
	<strong style="color: red;">Người sale:</strong> 
	@foreach($sales as $sale)
		{{ $sale->name }} {{ $sale->tel }} |
	@endforeach
</span>
@endif -->


    <?php 
    $logs = \Modules\WebBill\Models\LeadContactedLog::where('lead_id', @$lead->id)->orderBy('id', 'desc')->limit(3)->get();
    ?>
    @foreach($logs as $log)
        <hr style=" margin: 0;">
        <div class="log-item" style="color: #000;">
            <i></i>
            <div class="log-content">
                <span><strong>{{ $log->title }}</strong></span>
                <p style="font-size: 13px; margin: 0;">{!! $log->note !!}</p>
            </div>
            <i style="font-size: 11px; color: #666;">{{ date('H:i d/m/Y', strtotime($log->created_at)) }}   - Bởi: {{ @$log->admin->name }}</i>
        </div>
    @endforeach
    <hr style=" margin: 0;">
    <div class="log-item" data-id="" style="color: #000;">
        <i></i>
        <div class="log-content">
            <p style="font-size: 13px; margin: 0;">Tạo mới</p>
        </div>
        <i style="font-size: 11px;">{{ date('H:i d/m/Y', strtotime($lead->created_at)) }}   - Bởi: {{ @$lead->admin->name }}</i>
    </div>
</div>