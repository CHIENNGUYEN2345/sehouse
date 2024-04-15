<a
        href="/admin/{{ $module['code'] }}/edit/{{ $item->id }}">{!! @$inner_html_first !!}{{ $item->{$field['name']} }}</a>
@include('admin.themes.metronic1.list.td.partials.row_actions')