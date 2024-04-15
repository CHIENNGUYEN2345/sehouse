@extends(config('core.admin_theme').'.template')
@section('main')
<?php
$day = isset($_GET['day']) ? $_GET['day'] : date('Y-m-d');
$timekeeping = \App\CRMEdu\Models\Timekeeping::where('admin_id', \Auth::guard('admin')->user()->id)->where('day', $day)->first();
?>
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_exit" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">Tạo mới {{ $module['label'] }}
                            </h3>
                        </div>
                        <div class="kt-portlet__head-toolbar">
                            <a href="/admin/{{ $module['code'] }}" class="btn btn-clean kt-margin-r-10">
                                <i class="la la-arrow-left"></i>
                                <span class="kt-hidden-mobile">Quay lại</span>
                            </a>
                            <div class="btn-group">
                                @if(!is_object($timekeeping))
                                    @if(in_array($module['code'].'_add', $permissions))
                                        <button type="submit" class="btn btn-brand">
                                            <i class="la la-check"></i>
                                            <span class="kt-hidden-mobile">Lưu</span>
                                        </button>
                                        
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        @if(is_object($timekeeping))
            <span class="alert alert-danger">Ngày này đã tạo chấm công rồi!</span>
        @endif

        <div class="row">
            <div class="col-xs-12 col-md-8">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                    <div class="kt-portlet__head">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">
                                Lịch sử công việc trong ngày
                            </h3>
                        </div>
                    </div>
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first contacted_log_list">

                                <?php 
                                $count_quan_tam = 0;
                                $saler_id = isset($_GET['saler_id']) ? $_GET['saler_id'] : \Auth::guard('admin')->user()->id;
                                
                                $logs = [];
                                $lead_contacted_logs = \App\CRMEdu\Models\LeadContactedLog::select('lead_id', 'note', 'created_at', 'type')->where('admin_id', $saler_id)
                                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($day)))
                                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($day)))->orderBy('id', 'asc')->get();
                                foreach($lead_contacted_logs as $v) {
                                    $tinh_trang = $v->type == 'lead_quan_tam_lai' ? 'Quan tâm lại' : '';

                                    $logs[strtotime($v->created_at)] = '<td>Tương tác</td>
                                    <td><a href="/admin/lead/edit?code=' . @$v->lead->tel .'-' . date('d-m-Y', strtotime(@$v->lead->created_at)) . '-' . @$v->lead_id . '" target="_blank">' . @$v->lead->name . '</a></td>
                                    <td class="'.str_slug(@$v->lead->rate, '-').'">'.@$v->lead->rate.'</td>
                                    <td style="text-align:left;">' . $v->note . '</td>
                                    <td>'.@$v->lead->saler_ids.'</td>';

                                    if (in_array(@$v->type, ['lead_quan_tam_lai'])) {
                                        $count_quan_tam ++;
                                    }
                                }

                                $leads = \App\CRMEdu\Models\Lead::select('id', 'tel', 'name', 'created_at')->where('admin_id', \Auth::guard('admin')->user()->id)
                                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($day)))
                                    ->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($day)))->orderBy('id', 'asc')->get();
                                foreach($leads as $v) {
                                    $logs[strtotime($v->created_at)] = '<td>Tạo mới</td><td>' . @$v->lead->name . ': <a href="/admin/lead/edit?code=' . $v->tel .'-' . date('d-m-Y', strtotime($v->created_at)) . '-' . $v->id . '" target="_blank">' . $v->tel . '</a></td>
                                        <td>'.$v->rate.'</td>
                                        <td></td>
                                        <td></td>';

                                    if (in_array(@$v->rate, ['Đang tìm hiểu', 'Care dài', 'Quan tâm cao', 'Cơ hội'])) {
                                        $count_quan_tam ++;
                                    }
                                }

                                $coun_lead_contacted_logs = \App\CRMEdu\Models\LeadContactedLog::where('admin_id', $saler_id)
                                                    ->where('created_at', '>=', date('Y-m-d 00:00:00', strtotime($day)))->where('created_at', '<=', date('Y-m-d 23:59:59', strtotime($day)))
                                                    ->whereNotIn('note', ['Không nghe', 'Sai số'])->count();
                                ?>

                                @if(is_object($timekeeping))
                                    <strong>Tổng giờ làm: {{ $timekeeping->time }}</strong>
                                    <br>
                                    <strong>Công việc khác:</strong> {{ $timekeeping->job_other }}
                                @endif

                                <strong>Tổng cuộc gọi:</strong> {{ number_format(count($logs), 0, '.', '.') }}&nbsp;&nbsp;&nbsp;
                                <strong>Nghe máy:</strong> {{ $coun_lead_contacted_logs }}&nbsp;&nbsp;&nbsp;
                                <strong>Tổng quan tâm mới: </strong> {{ $count_quan_tam }}
                                
                                <table class="table-lich-su-hanh-dong">
                                    <thead>
                                        <tr>
                                            <th>STT</th>
                                            <th>Phút</th>
                                            <th>Giây</th>
                                            <th>Hành động</th>
                                            <th>Đầu mối</th>
                                            <th>Tình trạng</th>
                                            <th>Kết quả tương tác</th>
                                            <th>Sale</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $i = 1;?>
                                    @foreach($logs as $k => $v)
                                        <tr>
                                            <td><?php echo $i; $i++;?></td>
                                            <td>{{ date('H:i', $k) }}</td>
                                            <td>{{ date('s', $k) }}</td>
                                            {!! $v !!}
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
                <!--end::Portlet-->
            </div>
            <div class="col-xs-12 col-md-4">
                <!--begin::Portlet-->
                <div class="kt-portlet">
            
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">

                                <div class="form-group-div form-group col-md-12" id="form-group-day">
                                    <label for="day">Ngày chấm công </label>
                                    <div class="col-xs-12">
                                        <input type="date" name="day" class="form-control " id="day" value="{{ isset($_GET['day']) ? $_GET['day'] : date('Y-m-d') }}">                                                <span class="form-text text-muted"></span>
                                        <span class="text-danger"></span>
                                    </div>
                                </div>

                                

                                @foreach($module['form']['tab_2'] as $field)
                                    @if($field['type'] == 'custom')
                                        @include($field['field'], ['field' => $field])
                                    @else
                                        <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                                            <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                                    <span class="color_btd">*</span>@endif</label>
                                            <div class="col-xs-12">
                                                @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                                                <span class="form-text text-muted">{!! @$field['des'] !!}</span>
                                                <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>
            <!--end::Portlet-->
            </div>
        </div>
    </form>
    <script type="text/javascript">
        $('input[name=day]').change(function() {
            window.location.href = "/admin/timekeeping/add?day=" + $('input[name=day]').val();
        });

    </script>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8" href="{{ asset(config('core.admin_asset').'/css/form.css') }}">
    <style>
        .contacted_log_list ul li {
            list-style: auto;
        }
        table.table-lich-su-hanh-dong td, table.table-lich-su-hanh-dong th {
            border: 1px dotted;
            padding: 5px;
            text-align: center;
        }
        .dang-tim-hieu {
            color: green;
        }
    </style>
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>

    <script type="text/javascript" src="{{ asset('tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('tinymce/tinymce_editor.js') }}"></script>
    <script type="text/javascript">
        editor_config.selector = ".editor";
        editor_config.path_absolute = "{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" }}/";
        tinymce.init(editor_config);
    </script>
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}"></script>
@endsection
