<?php
$date = [
    'Monday' => 'Thứ 2',
    'Tuesday' => 'Thứ 3',
    'Wednesday' => 'Thứ 4',
    'Thursday' => 'Thứ 5',
    'Friday' => 'Thứ 6',
    'Saturday' => 'Thứ 7',
    'Sunday' => 'Chủ nhật'
];
$time_ids = explode('|', $item->{$field['name']});
foreach ($time_ids as $v) {
    if ($v != "") {
        echo '<p style="margin-right: 5px;
background-color: #1BC5BD;
color: white; text-align: center;
border-radius: 2px; display: inline-block;
width: unset; font-weight: bold;
padding: 3px;" >' . @$date[$v] . '</p>';

    }
}