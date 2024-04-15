<a href="/admin/{{ $module['code'] }}/{{ $item->id }}{{ isset($_GET['course_id']) ? '?course_id=' . $_GET['course_id'] : '' }}"
   style="    font-size: 14px!important;"
   class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xem</a>

@if( (\Auth::guard('admin')->user()->id == $item->admin_id &&
        in_array($module['code'] . '_delete', $permissions)  &&
        @\Modules\EduMarketing\Models\MaketingMail::find(@$_GET['course_id'])->status != 0)
         || in_array('super_admin', $permissions)
         || (!isset($_GET['course_id']) && @$item->status == 1) )
    | <a href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}{{ isset($_GET['course_id']) ? '?course_id=' . $_GET['course_id'] : '' }}"
       style="    font-size: 14px!important;" title="Xóa bản ghi"
       class="delete-warning {{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xóa</a>
@endif
