<div class="modal fade" id="confirm-action-modal" role="dialog" style="z-index:1060;">
    <div class="modal-dialog">
        <div class="modal-content" style="width:100%;height:100%">
            <div class="modal-header">
                <h4 class="modal-title">{{trans('admin.are_you_sure')}}</h4>
                <button type="button" class="close" data-dismiss="modal"></button>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
                <a class="btn btn-danger" id="confirm-action-yes" href="javascript:void(0)">{{trans('admin.yes')}}</a>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('admin.no_back')}}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        $(document).on('click', '.confirm-action', function (e) {
            e.preventDefault();
            var url = $(this).attr('href');
            $('#confirm-action-yes').attr('href', url);
            $('#confirm-action-modal').modal('show');
        });
    </script>
@endpush
