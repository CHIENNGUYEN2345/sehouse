<div class="modal fade" id="something-went-wrong" role="dialog" style="z-index:1060;">
    <div class="modal-dialog">
        <div class="modal-content" style="width:100%;height:100%">
            <div class="modal-header">
                <h4 class="modal-title">{{trans('admin.have_error')}}</h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger reload">{{trans('admin.load_again')}}</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('admin.quit')}}</button>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript">
        $('.reload').click(function () {
            location.reload();
        });
    </script>
@endpush