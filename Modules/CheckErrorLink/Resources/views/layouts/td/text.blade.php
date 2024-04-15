<a href="{{ route('list-link',['id'=>$item->id])   }}"
   style="font-size: 14px!important;">{!! $item->{$field['name']} !!}</a>
<div class="row-actions" style="    font-size: 13px;">
    <span class="edit" title="ID của bản ghi">ID: {{ @$item->id }} | </span>
    @if(in_array('super_admin', $permissions))
        <span class="trash"><a class="delete-warning"
                               href="{{ url('/admin/'.$module['code'].'/delete/' . $item->id) }}"
                               title="Xóa bản ghi">Xóa</a> | </span>
        <span class="trash"><a class=""
                               href="{{ url('/admin/'.$module['code'].'/' . $item->id) }}"
                               title="Chỉnh sửa">Chỉnh sửa</a> | </span>
        <span class="trash"><a class=""
                               href="{{ route('list-link',['id'=>$item->id])   }}"
                               title="Xem danh sách link">Xem danh sách link</a> | </span>
        <span class="trash"><a class=""
                               href="{{ route('check_error_link.run',['id'=>$item->id])   }}"
                               title="Quét theo domain">Quét theo domain</a> | </span>
    @endif
</div>
