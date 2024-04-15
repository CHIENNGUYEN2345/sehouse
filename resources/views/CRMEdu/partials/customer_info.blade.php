<?php
if(!isset($field_name)) $field_name = 'customer_id';
?>
<div class="kt-portlet">
    <!--begin::Form-->
    <div class="kt-widget__content info-{{ $field_name }}">
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>Họ & tên:</strong></span>
            <a class="customer_name info-field" href="/admin/user/edit/{{ @$result->user->id }}" class="kt-widget__data" target="_blank">{{ @$result->user->name }}</a>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>SĐT:</strong></span>
            <span class="kt-widget__data customer_tel info-field">{{ @$result->user->tel }}</span>
        </div>
        <div class="kt-widget__info">
            <span class="kt-widget__label"><strong>Email:</strong></span>
            <span class="kt-widget__data customer_email info-field">{{ @$result->user->email }}</span>
        </div>
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
        console.log(customer_id);

        $.ajax({
            url : '/admin/user/ajax-get-info',
            data: {
                id : customer_id
            },
            success: function (resp) {
                $('.info-{{ $field_name }} .info-field').html('');
                if (resp.status) {
                    console.log(resp.data.tel);
                    $('.info-{{ $field_name }} .customer_image').attr('src', resp.data.image);
                    $('.info-{{ $field_name }} .customer_name').html(resp.data.name);
                    $('.info-{{ $field_name }} .customer_email').html(resp.data.email);
                    $('.info-{{ $field_name }} .customer_tel').html(resp.data.tel);

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