<a href="/admin/{{ @$field['object'] }}/{{ @$item->{$field['object']}->id }}"
   target="_blank">
   {!! @\App\CRMDV\Models\BillReceipts::where('price', '>', 0)->where('bill_id', $item->id)->sum('price') < @$item->total_price_contract ? '<span style="color:red;">Chưa hết</span>' : 'Đã hết' !!}
</a>
