<style>
	.phu_luc_hd table td {
		border: 1px dotted #ccc;
	}
	.phu_luc_hd .kt-section.kt-section--first {
		overflow: scroll;
	}
</style>
<div class="kt-portlet phu_luc_hd">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Phụ lục hợp đồng
            </h3>
            <a href="/admin/bill/add?bill_parent={{ @$result->id }}" class="btn btn-brand btn-elevate btn-icon-sm" target="_blank">
			<i class="la la-plus"></i>
			Tạo mới
			</a>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <?php  
                $bill_childs = \App\CRMDV\Models\Bill::select('id', 'domain', 'registration_date', 'total_price_contract', 'total_price', 'service_id')
						->where('bill_parent', @$result->id)
						->orderBy('registration_date', 'desc')->get();
                ?>
                @if(count($bill_childs) > 0)
	                <table>
	                	<thead>
	                		<tr>
	                			<th>Tên miền</th>
								<th>Dịch vụ</th>
	                			<th>Ngày ký</th>
	                			<th>Tổng tiền</th>
								<th>Doanh số</th>
								<th>Sale</th>
								<th>Hành động</th>
	                		</tr>
	                	</thead>
	                	<tbody>
							<?php
								$total_price_contract = 0;
                                $total_price = 0;
								?>
	                		@foreach($bill_childs as $v)
								<?php
									$total_price_contract += $v->total_price_contract;
									$total_price += $v->total_price;
									?>
	                			<tr>
	                				<td>{{ $v->domain }}</td>
									<td>{{ @$v->service->name_vi }}</td>
									<td>{{ date('d/m/Y', strtotime($v->registration_date)) }}</td>
									<td>{{ number_format($v->total_price_contract, 0, '.', '.') }}đ</td>
	                				<td>{{ number_format($v->total_price, 0, '.', '.') }}đ</td>
									<td>{{ @$v->saler->name }}</td>
									<td><a href="/admin/bill/edit/{{ $v->id }}">Xem</a></td>
	                			</tr>
	                		@endforeach
							<tr>
								<td colspan="3"><strong>TỔNG CỘNG</strong></td>
								<td>{{ number_format($total_price_contract, 0, '.', '.') }}đ</td>
								<td>{{ number_format($total_price, 0, '.', '.') }}đ</td>
							</tr>
	                	</tbody>
	                </table>
	            @endif
            </div>
        </div>
    </div>
</div>