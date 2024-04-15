@if($item->{$field['name']} == 0)
    <span class="kt-badge  kt-badge--danger kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}" data-id="{{ $item->id }}"
          style="cursor:pointer;" data-column="{{ $field['name'] }}">{{trans('admin.pause')}}</span>
@elseif($item->finish_send != null && strtotime($item->finish_send) <= time())
    <span class="kt-badge  kt-badge--warning kt-badge--inline kt-badge--pill"
          style="cursor:pointer;" data-column="{{ $field['name'] }}">Hết hạn</span>
@elseif($item->{$field['name']} == 1 && $item->name == 'Chúc mừng sinh nhật tự động')
    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}" data-id="{{ $item->id }}"
          style="cursor:pointer;" data-column="{{ $field['name'] }}">{{trans('admin.active')}}</span>
@elseif($item->{$field['name']} == 1 && \Modules\EduMarketing\Models\MarketingMailLog::where('marketing_mail_id', $item->id)->where('sent', '!=', 1)->where('error', '!=', 1)->count() > 0)
    <span class="kt-badge kt-badge--success kt-badge--inline kt-badge--pill publish" data-url="{{@$field['url']}}" data-id="{{ $item->id }}"
          style="cursor:pointer;" data-column="{{ $field['name'] }}">{{trans('admin.active')}}</span>
@else
    <span class="kt-badge  kt-badge--secondary kt-badge--inline kt-badge--pill"
          style="cursor:pointer;" data-column="{{ $field['name'] }}">Hoàn thành</span>
@endif