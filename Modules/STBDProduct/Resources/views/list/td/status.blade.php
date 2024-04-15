<td style="text-align: center;" id="img{{ $field['name'] }}-{{ $item->{$module['primaryKey']} }}"><img
            data-id="{{ $item->{$module['primaryKey']} }}" class="publish"  data-column="{{ $field['name'] }}"
            style="cursor:pointer;"
            src="@if($item->{$field['name']}==1){{ '/public/images_core/icons/published.png' }}@else{{ '/public/images_core/icons/unpublish.png' }}@endif">
</td>