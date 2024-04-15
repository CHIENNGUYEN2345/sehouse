<?php
if(!isset($field_name)) $field_name = 'customer_id';
?>
<div class="kt-portlet">
    <!--begin::Form-->
    <div class="kt-widget__content">
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>Họ & tên:</strong></span>
            <a class="customer_name" href="/admin/admin/edit/{{ @$result->customer->id }}" class="kt-widget__data" target="_blank">{{ @$result->customer->name }}</a>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>SĐT:</strong></span>
            <span class="kt-widget__data" class="customer_tel">{{ @$result->customer->tel }}</span>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>Email:</strong></span>
            <span class="kt-widget__data" class="customer_email">{{ @$result->customer->email }}</span>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>Quyền:</strong></span>
            <span class="kt-widget__data" class="customer_role_name">{{ \App\Http\Helpers\CommonHelper::getRoleName(@$result->customer->id) }}</span>
        </div>
        {{--<div class="kt-widget__info">
            <span class="kt-widget__label">Tỉnh / Thành:</span>
            <span class="kt-widget__data" class="customer_province">{{ @$result->customer->province->name }}</span>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label">Quận / Huyện:</span>
            <span class="kt-widget__data" class="customer_district">{{ @$result->customer->district->name }}</span>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label">Phường / Xã:</span>
            <span class="kt-widget__data" class="customer_ward">{{ @$result->customer->ward->name }}</span>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label">Địa chỉ:</span>
            <span class="kt-widget__data" class="customer_address">{{ @$result->customer->address }}</span>
        </div>--}}
    </div>
    <!--end::Form-->
</div>

<script>
    $(document).ready(function () {
        getCustomerInfo();
        $('select[name={{ $field_name }}]').change(function () {
            getCustomerInfo();
        });
    });

    function getCustomerInfo() {
        var customer_id = $('select[name={{ $field_name }}]').val();
        $.ajax({
            url : '/admin/admin/ajax-get-info',
            data: {
                id : customer_id
            },
            success: function (resp) {
                if (resp.status) {
                    $('.info-{{ $field_name }} .customer_image').attr('src', resp.data.image);
                    $('.info-{{ $field_name }} .customer_name').html(resp.data.name);
                    $('.info-{{ $field_name }} .customer_email').html(resp.data.email);
                    $('.info-{{ $field_name }} .customer_tel').html(resp.data.tel);
                    $('.info-{{ $field_name }} .customer_role_name').html(resp.data.role_name);
                    // $('#customer_province').html(resp.data.province_name);
                    // $('#customer_district').html(resp.data.district_name);
                    // $('#customer_ward').html(resp.data.ward_name);
                    // $('#customer_address').html(resp.data.address);
                }
            },
            error: function () {
                alert('Có lỗi xảy ra! Vui lòng load lại website và thử lại!');
            }
        });
    }
</script>