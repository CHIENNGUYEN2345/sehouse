<?php
$field = ['name' => 'iframe', 'type' => 'iframe', 'class' => 'col-xs-12 col-md-8 padding-left'
    , 'src' => '/admin/bill_histories?bill_parent={id}', 'inner' => 'style="min-height: 400px;"'];
?>
<div class="kt-portlet">
    <div class="kt-portlet kt-portlet--height-fluid">
        <div class="kt-portlet__head">
            <div class="kt-portlet__head-label">
                <h3 class="kt-portlet__head-title">
                    Phụ lục HĐ
                </h3>
            </div>
        </div>

        <div class="kt-portlet__body">
            @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
        </div>
    </div>
</div>
