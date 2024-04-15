<!--begin::Portlet-->
<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Giá tiền
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <?php
                $price = json_decode(@$result->price);
                ?>
                <?php
                $field = ['name' => 'price', 'label' => 'Giá tiền'];
                ?>
                <div class="form-group form-group-dynamic" id="form-group-{{ $field['name'] }}">
                    <div class="col-xs-12">
                        @include("CRMWoo.form.fields.price", ['field' => $field, 'data' => $price])
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--end::Form-->
</div>
<!--end::Portlet-->
<style>
    .fieldwrapper > div {
        display: inline-block;
    }
</style>
<script>
    $('.add-contact-info').click(function () {
        console.log('fd');
    });
</script>