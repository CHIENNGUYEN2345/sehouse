<tr>
    <td class="item-{{$field['name']}}">{{ $field['label'] }} : <a
                href="/admin/{{ $field['object'] }}//{{ @$item->{$field['object']}->id }}"
                target="_blank">
            {{ @$item->{$field['object']}->{$field['display_field']} }}
        </a></td>
</tr>