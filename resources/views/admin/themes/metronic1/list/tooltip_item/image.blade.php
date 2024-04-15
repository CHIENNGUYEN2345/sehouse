<tr>
    <td class="item-{{$field['name']}}"><a href="{{ asset('filemanager/userfiles/' . $item->{$field['name']}) }}" title="Xem ảnh" target="_blank"><img
                    src="{{ CommonHelper::getUrlImageThumb($item->{$field['name']}, 100, 100) }}"
                    style="max-width: 97px;"/></a></td>
</tr>