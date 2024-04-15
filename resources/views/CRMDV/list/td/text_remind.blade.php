<?php
$time_ids = explode('|', $item->{$field['name']});
foreach ($time_ids as $v) {
    if($v != "") {
    echo '<p style="margin-right: 5px;
background-color: #1BC5BD;
color: white; text-align: center;
border-radius: 2px; display: inline-block;
width: unset; font-weight: bold;
padding: 3px;" >' . $v . '</p>';
    }
}