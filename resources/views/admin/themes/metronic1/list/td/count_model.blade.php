<?php $model = new $field['model'];?>
<a href="/admin/{{ $field['object'] }}?{{ $field['name'] }}={{ $item->id }}"
   target="_blank">{{ number_format($model->where($field['name'], 'like', '%|' . @$item->id . '|%')->count(), 0, '.', '.') }}</a>