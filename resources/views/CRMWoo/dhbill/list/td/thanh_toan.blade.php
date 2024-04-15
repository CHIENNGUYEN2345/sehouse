<a href="/admin/{{ @$field['object'] }}/{{ @$item->{$field['object']}->id }}"
   target="_blank">
   {!! @$item->total_received < @$item->total_price_contract ? '<span style="color:red;">Chưa hết</span>' : 'Đã hết' !!}
</a>
