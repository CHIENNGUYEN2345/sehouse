<tr>
    <td class="item-{{$field['name']}}">{{ $field['label'] }} : {{ date('d-m-Y', strtotime(@$item->{$field['name']})) }}</td>
</tr>