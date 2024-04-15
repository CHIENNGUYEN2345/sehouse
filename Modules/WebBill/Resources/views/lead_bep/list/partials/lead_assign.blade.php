<div class="modal fade" id="leadAssign_modal" role="dialog" style="    z-index: 1041;">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content" style="width:100%;height:100%">
            <div class="modal-header">
                <h4 class="modal-title">Chuyển đầu mối</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form class="kt-form kt-form--fit kt-margin-b-20" action="/admin/lead/assign" method="POST">
                    <input type="hidden" name="lead_ids" value="">
                    <?php
                    $field = ['name' => 'sale_id', 'type' => 'select2_ajax_model', 'label' => 'Người nhận', 'model' => \App\Models\Admin::class, 'object' => 'admin', 'display_field' => 'name'];
                    ?>
                    @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                    <button class="btn btn-primary btn-brand--icon" id="kt_search" type="submit">
                    <span>Chuyển</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function leadAssign(){
        var ids = '';
        $('.ids:checkbox:checked').each(function (i) {
            ids += $(this).val() + ',';
        });
        if (ids.length == 0) {
            alert('Bạn chưa chọn bản ghi nào!');
        } else {
            $('input[name=lead_ids]').val(ids);
            $('#leadAssign_modal').modal();
        }
    }
</script>