@if(isset($tooltip_info))
    @foreach($tooltip_info as $field)
        @include('admin.themes.metronic1.list.tooltip_item.'.$field['type'])
    @endforeach
@endif