<?php
$giay = 5;
$toi_da_truy_cap = 5;
$thoi_gian_sleep = 3;
session_start();

$time = round(time() / $giay);

if (isset($_SESSION['truy_cap'][$time])) {
    $_SESSION['truy_cap'][$time] ++;
} else {
    $_SESSION['truy_cap'][$time] = 1;
}
$_SESSION['truy_cap'] = [
    $time => $_SESSION['truy_cap'][$time]
];

if ($_SESSION['truy_cap'][$time] > $toi_da_truy_cap) {
    sleep($thoi_gian_sleep);
    echo 'quá 5';
}

var_dump($_SESSION['truy_cap']);
die('e');