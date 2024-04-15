<?php $k = time() . rand(1,1000);?>
<li style="margin-left: 20px; margin-bottom: 39px; border-bottom: 1px solid #ccc;">
    <a class="kt-avatar__cancel center-content attr-delete" data-toggle="kt-tooltip" data-original-title="Xóa hàng này" style="position: absolute;
    right: 0;
    top: -22px;
    background: #fff;
    padding: 3px;
    color: red;
    cursor: pointer;
    border: 1px solid red;
    border-radius: 47%;">
        <i class="fa fa-trash"></i>
    </a>
    <div class="col-md-5 item-field">
        <?php $field = ['name' => 'category_id' . $k, 'type' => 'select2_ajax_model', 'object' => 'category_product', 'class' => '', 'label' => 'Danh mục',
            'model' => \Modules\STBDAutoUpdatePriceWSS\Entities\Category::class, 'display_field' => 'name', 'display_field2' => 'id', 'value' => @$cat->category_id];?>
        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
    </div>
    <div class="col-md-7 item-field">
        <?php $field = ['name' => 'link_crawl' . $k, 'type' => 'text', 'class' => 'required', 'label' => 'Lấy từ Link',
            'value' => @$cat->link_crawl];?>
        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
    </div>
</li>