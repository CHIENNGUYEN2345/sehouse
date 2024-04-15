<tr>
    <td class="item-{{$field['name']}}">{{ $field['label'] }} : <a
                href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}">{!! @$inner_html_first !!}{{ $item->{$field['name']} }}</a></td>
</tr>