<?php

function checkChamCong($item, $ls_cham_cong, $tong_cong, $di_muon, $cau_hinh) {

    $cham_cong_trung = false;
    $di_muon_co_phep = false;
    $phut_tre = 'Đúng giờ';

    // $item->time = '2022-12-11 07:23:00';
    if (strtotime($item->time) < strtotime(date('Y-m-d 10:00:00', strtotime($item->time)))) {
        //  Buổi sáng đến làm

        $ls_cham_cong[date('d', strtotime($item->time)) . 's_den'] = 1;

        if (isset($tong_cong[date('d', strtotime($item->time)) . 's'])) {
            $cham_cong_trung = true;
        }
        $tong_cong[date('d', strtotime($item->time)) . 's'] = 1;


        if (strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time)))) {
            //  đi muộn
            if ($item->status != 1) {
                //  ko có phép
                $di_muon[date('d', strtotime($item->time)) . 's'] = 1;  //  đi muộn ngày này buổi sáng
            } elseif ($item->status == 1) {
                $di_muon_co_phep = true;
            }

            $phut_tre = ceil((strtotime($item->time) - strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_sang'], strtotime($item->time)))) / 60);


        } else {
            //  đúng giờ

        }
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 10:00:00', strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 12:15:00', strtotime($item->time)))) {
        //  buổi sáng đi về

        if (isset($tong_cong[date('d', strtotime($item->time)) . 's'])) {
            $cham_cong_trung = true;
        }
        $tong_cong[date('d', strtotime($item->time)) . 's'] = 1;

        $ls_cham_cong[date('d', strtotime($item->time)) . 's_ve'] = 1;
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 12:15:00', strtotime($item->time))) & strtotime($item->time) < strtotime(date('Y-m-d 15:00:00', strtotime($item->time)))) {
        //  Buổi chiều đến làm

        $ls_cham_cong[date('d', strtotime($item->time)) . 'c_den'] = 1;

        if (isset($tong_cong[date('d', strtotime($item->time)) . 'c'])) {
            $cham_cong_trung = true;
        }
        $tong_cong[date('d', strtotime($item->time)) . 'c'] = 1;

        if ( ( strtotime($item->time) > strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time))) )) {
            //  đi muộn
            if ($item->status != 1) {
                $di_muon[date('d', strtotime($item->time)) . 'c'] = 1;  //  đi muộn ngày này buổi chiều
            } elseif ($item->status == 1) {
                $di_muon_co_phep = true;
            }

            $phut_tre = ceil((strtotime($item->time) - strtotime(date('Y-m-d ' . @$cau_hinh['gio_lam_chieu'], strtotime($item->time)))) / 60);

        } else {
            //  đúng giờ

        }
    } elseif (strtotime($item->time) >= strtotime(date('Y-m-d 15:00:00', strtotime($item->time))) ) {
        //  Buổi chiều về

        if (isset($tong_cong[date('d', strtotime($item->time)) . 'c'])) {
            $cham_cong_trung = true;
        }
        $tong_cong[date('d', strtotime($item->time)) . 'c'] = 1;


        $ls_cham_cong[date('d', strtotime($item->time)) . 'c_ve'] = 1;
    }

    return [
        'ls_cham_cong' => $ls_cham_cong,
        'tong_cong' => $tong_cong,
        'di_muon' => $di_muon,
        'di_muon_co_phep' => $di_muon_co_phep,
        'phut_tre' => $phut_tre,
        'cham_cong_trung' => $cham_cong_trung,
    ];
}