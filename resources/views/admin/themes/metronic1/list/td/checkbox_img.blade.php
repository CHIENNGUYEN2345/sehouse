<img
        data-id="{{ $item->id }}" class="{{$field['name']}}"
        style="cursor:pointer;"
        src="@if($item->{$field['name']}==1){{ '/images_core/icons/published.png' }}@else{{ '/images_core/icons/unpublish.png' }}@endif">