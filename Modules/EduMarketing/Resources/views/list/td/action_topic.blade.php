<a href="/admin/{{ $module['code'] }}/{{ $item->id }}{{ '?lesson_item_id=' . @$_GET['lesson_item_id']}}"
   style="    font-size: 14px!important;"
   class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xem</a>

@if( (\Auth::guard('admin')->user()->id == $item->admin_id &&
        in_array($module['code'] . '_delete', $permissions)  &&
        @\Modules\EduMarketing\Models\MarketingMailLog::find(@$_GET['lesson_item_id'])->status != 0)
         || in_array('super_admin', $permissions)
         || (!isset($_GET['lesson_item_id']) && @$item->status == 1) )
    | <a href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}{{ isset($_GET['lesson_item_id']) ? '?lesson_item_id=' . $_GET['lesson_item_id'] : '' }}"
         style="    font-size: 14px!important;" title="Xóa bản ghi"
         class="delete-warning {{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xóa</a>
@endif
