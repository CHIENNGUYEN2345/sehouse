@extends(config('core.admin_theme').'.template')
@section('main')
<?php
if (@$_GET['saler_id'] != null) {
    //  Nếu có lọc theo sale
    $whereSale = 'saler_id = ' . $_GET['saler_id'];
    $whereLikeSale = "saler_id LIKE '%|".$_GET['saler_id']."|%'";
} else {
    //  Nếu ko lọc theo sale
    if(in_array('view_all_data', $permissions)) {
        //  Nếu được xem toàn bộ data
        $whereSale = $whereLikeSale = '1=1';
    } else {
        //  Nếu ko được xem toàn bộ data thì truy vấn ra data của mình thôi
        $whereSale = 'saler_id = ' . \Auth::guard('admin')->user()->id;
        $whereLikeSale = "saler_id LIKE '%|".\Auth::guard('admin')->user()->id."|%'";   
    }
}

$whereRegistration_date = '1=1';
if (!is_null(@$_GET['from_date']) && @$_GET['from_date'] != '') {
    $whereRegistration_date .= " AND registration_date >= '" . date('Y-m-d 00:00:00', strtotime($_GET['from_date'])) . "'";
}
if (!is_null(@$_GET['to_date']) && @$_GET['to_date'] != '') {
    $whereRegistration_date .= " AND registration_date <= '" . date('Y-m-d 23:59:59', strtotime($_GET['to_date'])) . "'";
}
?>
<div class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid">
    <!--begin: Datatable -->
    <div class="row">
        <div class="col-xs-12 col-md-12">
            @include('CRMDV.dashboard.partials.nhac_nho.hd_sap_het_han')

        </div>
        <div class="col-xs-12 col-md-12">
            @include('CRMDV.dashboard.partials.nhac_nho.hd_no_tien')
        </div>
    </div>
    <!--end: Datatable -->
</div>
@endsection

@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/list.css') }}">
    
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset(config('core.admin_asset').'/js/list.js') }}"></script>
    @include(config('core.admin_theme').'.partials.js_common')
@endsection
@push('scripts')
    @include(config('core.admin_theme').'.partials.js_common_list')
@endpush
