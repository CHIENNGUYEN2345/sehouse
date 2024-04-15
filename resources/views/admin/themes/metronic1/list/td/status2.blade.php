@if($item->{$field['name']}==1)
    @if(@$field['options'][$item->{$field['name']}] != '')
        <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}"
              data-id="{{ $item->id }}"
              style="cursor:pointer;"
              data-column="{{ $field['name'] }}">{{ @$field['options'][$item->{$field['name']}] }}</span>
    @endif
@else
    @if(@$field['options'][$item->{$field['name']}] != '')
        <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}"
              data-id="{{ $item->id }}"
              style="cursor:pointer;"
              data-column="{{ $field['name'] }}">{{ @$field['options'][$item->{$field['name']}] }}</span>
    @endif
@endif
