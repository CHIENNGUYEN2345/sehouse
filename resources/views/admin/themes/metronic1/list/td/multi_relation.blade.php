<?php
    if ($item->{$field['name']} == null || $item->{$field['name']} == '|' || $item->{$field['name']} == '||') {
        echo '';
    } else {
        $cat_id_arr = [];
        foreach (explode('|', $item->{$field['name']}) as $v) {

            if ($v != '') $cat_id_arr[] = $v;
        }

        $model = new $field['model'];
        $model = $model->select([$field['display_field']])->whereIn('id', $cat_id_arr)->pluck($field['display_field']);
        $html = '';
        foreach ($model as $v) {
            $html .= $v . ' | ';
        }
//        echo $html;
         echo substr($html, 0, -3);
    }
?>