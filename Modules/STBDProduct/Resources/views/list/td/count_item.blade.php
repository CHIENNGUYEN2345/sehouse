<?php
$model = new $field['model'];
$count = $model->where($field['name'], 'like', '%|' . $item->id . '|%')->count();
echo number_format($count, 0, '.', '.');