<a href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}"
        style="    font-size: 14px!important;" class="{{ isset($field['tooltip_info']) ? 'a-tooltip_info' : '' }}">{!! $item->{$field['name'] . '_' . $language} !!}</a>
@include('admin.themes.metronic1.list.td.partials.row_actions')
@if(isset($field['tooltip_info']))
    {{--{{dd($field['tooltip_info'])}}--}}
    <div id="tooltip-info-{{$field['name']}}" class="dropdown-menu div-tooltip_info"
         data-modal="{{ $module['modal'] }}"
         data-tooltip_info="{{ json_encode($field['tooltip_info']) }}"><img class="tooltip_info_loading"
                                                                            src="/images_core/icons/loading.gif">
    </div>
@endif