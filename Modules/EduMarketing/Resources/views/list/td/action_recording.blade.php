<a href="/admin/{{ $module['code'] }}/{{ $item->id }}{{ '?topic_id=' . (@$_GET['topic_id'])}}"
   style="    font-size: 14px!important;"
   class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xem</a>

@if( (\Auth::guard('admin')->user()->id == $item->admin_id &&
        in_array($module['code'] . '_delete', $permissions)  &&
        @\Modules\EduMarketing\Models\Topic::find(@$_GET['topic_id'])->status != 0)
         || in_array('super_admin', $permissions)
         || (!isset($_GET['topic_id']) && @$item->status == 1) )
    |
    <a href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}{{ isset($_GET['topic_id']) ? '?topic_id=' . $_GET['topic_id'] : '' }}"
       style="    font-size: 14px!important;" title="Xóa bản ghi"
       class="delete-warning {{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xóa</a>
@endif
