<div class="modal fade" id="popup-create-tag_product" role="dialog" style="z-index:1060;">
    <form action="" id="form-create-tag_product">
        <div class="modal-dialog">
            <div class="modal-content" style="width:100%;height:100%">
                <div class="modal-header">
                    <h4 class="modal-title">Tạo mới từ khóa</h4>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <?php
                    $field_slug = ['name' => 'slug', 'type' => 'slug', 'class' => '', 'label' => 'Slug', 'des' => 'Đường dẫn sản phẩm trên thanh địa chỉ'];
                    $field_parent_id = ['name' => 'parent_id', 'type' => 'custom', 'field' => 'jdespost::form.fields.select_model_tree', 'class' => '', 'label' => 'Danh mục cha', 'model' => \Modules\JdesPost\Models\Category::class];
                    ?>
                    <div class="form-group-div form-group " id="form-group-category">
                        <label for="name">Tên từ khóa <span class="color_btd">*</span></label>
                        <div class="col-xs-12">
                            <input type="text" name="name" class="form-control required" id="category" value="">
                            <span class="form-text text-muted"></span>
                            <span class="text-danger"></span>
                        </div>
                    </div>
                    <div class="form-group-div form-group " id="form-group-slug">
                        <label for="name">Đường dẫn<span class="color_btd">*</span></label>
                        <div class="col-xs-12">
                            @include(config('core.admin_theme').".form.fields.".$field_slug['type'], ['field' => $field_slug])
                            <span class="form-text text-muted"></span>
                            <span class="text-danger"></span>
                        </div>
                    </div>

                    {{--                @include($field_parent_id['field'], ['field' => $field_parent_id])--}}
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success">Thêm danh mục</button>
                    <a class="btn btn-default" data-dismiss="modal">Trở về</a>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).on('click', '.btn-popup-create-tag_product', function (e) {
        e.preventDefault();
        $('#form-create-tag_product').trigger("reset");
        $('#popup-create-tag_product').modal('show');

    });
    $('#form-create-tag_product').validate({
        rules: {
            name: {
                required: true,
            },
            slug: {
                required: true,
            },
        },
        messages: {
            name: {
                required: "<p style='color: red'>Tên từ khóa không được để trống</p>",
            },
            slug: {
                required: "<p style='color: red'>Đường dẫn không được để trống</p>",
            },
        },
        submitHandler: function (form) {
            //code in her
            event.preventDefault();

            var data = $('#form-create-tag_product').serializeArray().reduce(function (obj, item) {
                obj[item.name] = item.value;
                obj['status'] = 1;
                return obj;
            }, {});
            console.log(data)
            $.ajax({
                url: '/admin/tag_product/add',
                type: 'POST',
                data: data,
                success: function (resp) {
                    if (resp.status) {
                        $('#form-create-tag_product .close').click();
                        toastr.success('Tạo thành công');
                    } else {
                        toastr.error(resp.error[0]);
                    }
                },
                error: function () {
                    alert('Có lỗi xảy ra! Vui lòng load lại website và thử lại.');
                }
            });
        }
    });
</script>
