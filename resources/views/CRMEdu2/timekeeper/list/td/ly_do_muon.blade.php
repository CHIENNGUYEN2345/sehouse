
@if ((strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 11:00:00', strtotime($item->time)))) || (strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 16:00:00', strtotime($item->time)))))

	@if($item->{$field['name']}==1)
	        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}" data-id="{{ $item->id }}"
	              style="cursor:pointer;" data-column="{{ $field['name'] }}">Có lý do</span>
	@else
	        <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}" data-id="{{ $item->id }}"
	              style="cursor:pointer;" data-column="{{ $field['name'] }}">Không lý do</span>
	@endif
@endif