<h3>Danh sách các link web lỗi</h3>

<table>
    <thead style="background: #0a8cf0">
    <tr style="height: 40px">

        <th style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #4CAF50;color: white;">
            Tên miền
        </th>
        <th style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #4CAF50;color: white;">
            Đường dẫn
        </th>
        <th style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #4CAF50;color: white;">
            Mã lỗi
        </th>
        <th style="padding-top: 12px;padding-bottom: 12px;text-align: left;background-color: #4CAF50;color: white;">
            Mô tả lỗi
        </th>
    </tr>
    </thead>
    <tbody>
    <?php
    $time_status = false;
    ?>
    @foreach($data['list'] as $id=>$group)
        <?php
        $domain = \Modules\CheckErrorLink\Models\DomainCheck::find($id);
        ?>
        @foreach($group as $key=>$item)
            <tr style="height: 35px">
                @if($key == 0)
                <td style="border: 1px solid #ddd;padding: 8px;"rowspan="{{count($group)}}" >
                    {{$domain->name}}</td>
                @endif
                <td style="border: 1px solid #ddd;padding: 8px;">{{$item->links}}</td>
                <td style="border: 1px solid #ddd;padding: 8px;">{{$item->error_code}}</td>
                <td style="border: 1px solid #ddd;padding: 8px;">{{$item->error_messenger}}</td>
            </tr>
            <?php
            $time_scan = $item->time_scan;
            $time_status = true;
            ?>
        @endforeach
    @endforeach
    </tbody>
</table>
@if(  $time_status )
    <p>Thời gian quét: {{ date('d/m/Y H:i:s', strtotime(@$time_scan)) }}</p>
@endif
<a href="{{ $data['link_view_more'] }}">Xem chi tiết tại admin >></a>