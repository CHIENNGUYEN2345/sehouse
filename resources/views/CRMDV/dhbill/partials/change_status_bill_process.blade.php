<div class="modal fade" id="bill_process_modal" role="dialog" style="    z-index: 1041;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:100%;height:100%">
            <div class="modal-header">
                <h4 class="modal-title">Chuyển trạng thái</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form class="kt-form kt-form--fit kt-margin-b-20" action="/admin/dhbill/change-status" method="POST">
                    <input type="hidden" name="bill_ids" value="">

                    <div class="form-group-div form-group">
                        <?php
                        $field = ['name' => 'status', 'type' => 'select', 'options' => [
                                    'Nghiệm thu L3 & thu thập YCTK L4' => 'Nghiệm thu L3 & thu thập YCTK L4',
                                    'Khách xác nhận xong' => 'Khách xác nhận xong',
                                    'Kết thúc' => 'Kết thúc',
                                    'Tạm dừng' => 'Tạm dừng',
                                ], 'label' => 'Trạng thái mới', 'value' => '-'];
                        ?>
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                    </div>

                    <button class="btn btn-primary btn-brand--icon" id="kt_search" type="submit">
                    <span>Chuyển</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function billProcessChangeStatus(){
        var ids = '';
        $('.ids:checkbox:checked').each(function (i) {
            ids += $(this).val() + ',';
        });
        if (ids.length == 0) {
            alert('Bạn chưa chọn bản ghi nào!');
        } else {
            $('input[name=bill_ids]').val(ids);
            $('#bill_process_modal').modal();
        }
    }
</script>