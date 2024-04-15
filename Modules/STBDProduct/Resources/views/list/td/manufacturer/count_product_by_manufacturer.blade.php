<a href="{{ route($field['route_name'], ['manufacture_id' => @$item->id]) }}">{{ number_format(@$field['model']::where('manufacture_id', $item->id)->count(), 0, '.', '.') }}</a>
