<a href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}{{ isset($_GET['marketing_mail_id']) ? '?marketing_mail_id=' . $_GET['marketing_mail_id'] : '' }}"
   style="    font-size: 14px!important;"
   class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xem</a>

@if( (\Auth::guard('admin')->user()->id == $item->admin_id &&
        in_array($module['code'] . '_delete', $permissions)  &&
        @\Modules\EduCourse\Models\MaketingMail::find(@$_GET['marketing_mail_id'])->status != 0)
         || in_array('super_admin', $permissions)
         || (!isset($_GET['marketing_mail_id']) && @$item->status == 1) )
    | <a href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}{{ isset($_GET['marketing_mail_id']) ? '?marketing_mail_id=' . $_GET['marketing_mail_id'] : '' }}"
         style="    font-size: 14px!important;" title="Xóa bản ghi"
         class="delete-warning {{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">Xóa</a>
@endif
