<div class="kt-portlet kt-portlet--height-fluid">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Bộ lọc
            </h3>
        </div>
    </div>
    <div class="kt-portlet__body">
        <form method="GET" action="" class="col-xs-12 col-md-12">
            <div class="col-sm-3 col-md-3" style="display: inline-block; float: left;">
                <div>
                    <?php $field = ['name' => 'admin_id', 'type' => 'select2_ajax_model', 'class' => '', 'label' => 'Nhân viên',
                        'model' => \App\Models\Admin::class, 'display_field' => 'name', 'object' => 'admin', 'value' => @$_GET['admin_id'], 'where' => $whereCompany];
                    ?>
                    @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                </div>
            </div>
            <div class="col-sm-9 col-md-9" style="display: inline-block; float: left;">
                <label style="">Từ ngày</label>
                <input type="date"
                       style=""
                       name="start_date" value="{{ $start_date }}">
                <label style="margin-left: 10px;">Đến ngày</label>
                <input type="date"
                       style=""
                       name="end_date" value="{{ $end_date }}">
                <input class="loc" type="submit" value="Lọc"
                       style="padding:7px 0px 7px 0px;width:70px;margin:13px 0 0 10px;border:1px solid #ccc;border-radius: 4px;">
                <a href="/admin/dashboard" class="loc">Xóa bộ lọc</a>
            </div>
        </form>
    </div>
</div>