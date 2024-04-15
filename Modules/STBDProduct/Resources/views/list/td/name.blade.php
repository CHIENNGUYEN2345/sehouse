<td class="item-{{$field['name']}}"><a
            href="{{ route($module['name'].'.getEdit', ['id'=>$item->{$module['primaryKey']}]) }}">{{ $item->{$field['name']} }}</a>
</td>