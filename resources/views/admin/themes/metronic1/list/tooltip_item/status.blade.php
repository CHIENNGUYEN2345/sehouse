<tr>
    <td class="item-{{$field['name']}}">{{ $field['label'] }} : <img
                data-id="{{ $item->id }}" class="publish"
                style="cursor:pointer;" data-column="{{ $field['name'] }}"
                src="@if($item->{$field['name']}==1){{ '/images_core/icons/published.png' }}@else{{ '/images_core/icons/unpublish.png' }}@endif"></td>
</tr>