<div class="kt-portlet kt-portlet--height-fluid kt-portlet--mobile">
    <div class="kt-portlet__head kt-portlet__head--lg kt-portlet__head--noborder kt-portlet__head--break-sm">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title bold uppercase">
                Lớp học sắp kết thúc
            </h3>
        </div>
    </div>

    <div class="kt-portlet__body kt-portlet__body--fit">
        <!--begin: Datatable -->
        <div class="kt-datatable kt-datatable--default kt-datatable--brand kt-datatable--scroll kt-datatable--loaded" id="kt_datatable_latest_orders" style="">
            <table class="kt-datatable__table" style="width: 100%;">
                <thead class="kt-datatable__head" style="overflow: unset;">
                <tr class="kt-datatable__row" style="left: 0px;">
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 25px;">STT</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Tên lớp học</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Ngày kết thúc</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 50px;">Chi tiết</span>
                    </th>
                    <th class="kt-datatable__cell kt-datatable__cell--sort">
                        <span style="width: 100px;">Số học sinh</span>
                    </th>
                </tr>
                </thead>
                <tbody class="kt-datatable__body ps ps--active-y" style="">
                @if($classrooms->count() > 0)
                    @foreach($classrooms as $classroom)
                        @php
                            $endDate = \Carbon\Carbon::parse($classroom->end_date);
                            $currentDate = \Carbon\Carbon::now();
                            $daysRemaining = $endDate->diffInDays($currentDate);
                        @endphp

                        @if($daysRemaining <= 15) <!-- Chỉ hiển thị những lớp học còn dưới 15 ngày nữa sẽ kết thúc -->
                        <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
                            <td data-field="STT" class="kt-datatable__cell">
                                <span style="width: 25px;">
                                    <span class="kt-font-bold">{{ $loop->iteration }}</span>
                                </span>
                            </td>
                            <td data-field="Tên lớp học" class="kt-datatable__cell">
                                <span style="width: 100px;">
                                    <a href="/admin/classroom/{{ $classroom->id }}"><span class="kt-font-bold">{{ $classroom->name }}</span></a>
                                </span>
                            </td>
                            <td data-field="Ngày kết thúc" class="kt-datatable__cell">
                                <span style="width: 100px;">
                                    <span class="kt-font-bold">{{ $classroom->end_date }}</span>
                                </span>
                            </td>
                            <td data-field="Chi tiết" class="kt-datatable__cell">
                                <span style="width: 50px;">
                                    <span class="kt-font-bold">{{ $classroom->note }}</span>
                                </span>
                            </td>
                            <td data-field="Danh sách học sinh" class="kt-datatable__cell">
                                <span style="width: 100px;">
                                    <!-- Đếm và hiển thị số lượng học sinh trong lớp học -->
                                    <?php
                                        $studentCount = App\CRMEdu\Models\Lead::where('classroom_id', $classroom->id)->count();
                                        echo $studentCount;
                                        ?>
                                </span>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
        <!--end: Datatable -->
        {{-- <div class="paginate">{{ $classrooms->render() }}</div> --}}
    </div>
</div>
