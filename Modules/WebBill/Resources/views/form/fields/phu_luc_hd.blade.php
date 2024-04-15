<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Phụ lục hợp đồng
            </h3>
            <a href="https://hbsoft.top/admin/bill/add?bill_parent={{ @$result->id }}" class="btn btn-brand btn-elevate btn-icon-sm">
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
                $bill_childs = \Modules\WebBill\Models\Bill::select('id', 'domain', 'registration_date', 'total_price')->where('bill_parent', @$result->id)->get();
                ?>
                @if(count($bill_childs) > 0)
	                <table>
	                	<thead>
	                		<tr>
	                			<th>Tên miền</th>
	                			<th>Ngày ký</th>
	                			<th>Doanh số</th>
	                		</tr>
	                	</thead>
	                	<tbody>
	                		@foreach($bill_childs as $v)
	                			<tr>
	                				<td>{{ $v->domain }}</td>
	                				<td>{{ date('d/m/Y', strtotime($v->registration_date)) }}</td>
	                				<td>{{ number_format($v->total_price, 0, '.', '.') }}đ</td>
	                			</tr>
	                		@endforeach
	                	</tbody>
	                </table>
	            @endif
            </div>
        </div>
    </div>
</div>