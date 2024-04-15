<a href="/admin/{{ $module['code'] }}/{{ $item->id }}"
   style="    font-size: 14px!important;"
   class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xem</a>

@if( (\Auth::guard('admin')->user()->id == $item->admin_id &&
        in_array('product_delete', $permissions)  &&
        @\Modules\STBDProduct\Models\ProductSale::find(@$_GET['id_product_sale'])->status != 0)
         || in_array('super_admin', $permissions)
         || (!isset($_GET['id_product_sale']) && @$item->status == 1) )
    | <a href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}"
       style="    font-size: 14px!important;" title="Xóa bản ghi"
       class="delete-warning {{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xóa</a>
@endif
