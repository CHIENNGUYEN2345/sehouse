<style>
    .item-action {
        font-size: 25px;
    }
</style>
<table class="table table-striped">
    <thead class="kt-datatable__head">
    <tr class="kt-datatable__row" style="left: 0px;">
        <th class="kt-datatable__cell kt-datatable__cell--sort ">
            {{trans('admin.file_name')}}
        </th>
        <th class="kt-datatable__cell kt-datatable__cell--sort ">
            {{trans('admin.date_created')}}
        </th>
        <th class="kt-datatable__cell kt-datatable__cell--sort ">
            {{trans('admin.action')}}
        </th>
    </tr>
    </thead>
    <tbody class="kt-datatable__body ps ps--active-y"
           style="max-height: 496px;">
    @if(is_dir(storage_path('app/file-backup')))
        <?php
        $files = array_diff(scandir(storage_path('app/file-backup')), array('.', '..'));
        $files = array_reverse($files);
        ?>
        @foreach($files as $key=>$file)
            @if($key < 3)
                <tr data-row="0" class="kt-datatable__row"
                    style="left: 0px;">
                    <td data-field="short_name"
                        class="kt-datatable__cell item-short_name">
                        <a href="{{route('downloadDB',['file_name'=>$file])}}"
                           style="font-size: 14px!important;"
                           class="">{{$file}}</a>
                    </td>
                    <td data-field="date"
                        class="kt-datatable__cell item-date">
                        {{  date('d-m-Y H:i:s',filemtime(storage_path('app/file-backup/'.$file)))}}
                    </td>
                    <td data-field="action"
                        class="kt-datatable__cell item-action ">
                        <a class="text-info" href="{{route('downloadDB',['file_name'=>$file])}}"
                           title="Tải xuống"><i class="flaticon2-download"></i></a>
                        <a class="delete-warning text-danger"
                           href="{{route('deleteDB',['file_name'=>$file])}}"
                           title="Xóa bản ghi"><i class="flaticon2-rubbish-bin"></i></a>
                    </td>
                </tr>
            @else
                <?php
                $file_url = storage_path('app/file-backup/' . $file);
                unlink($file_url);?>
            @endif
        @endforeach
    @else
        <tr data-row="0" class="kt-datatable__row"
            style="left: 0px;">
            <td colspan="3">
                {{trans('admin.does_not_exist_the_backup')}}
            </td>
        </tr>
    @endif
    </tbody>
</table>