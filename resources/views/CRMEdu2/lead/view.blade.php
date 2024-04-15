@extends(config('core.admin_theme').'.template_blank')
@section('main')
    <form class="kt-container  kt-container--fluid  kt-grid__item kt-grid__item--fluid {{ @$module['code'] }}"
          action="" method="POST"
          enctype="multipart/form-data">
        {{ csrf_field() }}
        <input name="return_direct" value="save_continue" type="hidden">
        <div class="row">
            <div class="col-lg-12">
                <!--begin::Portlet-->
                <div class="kt-portlet kt-portlet--last kt-portlet--head-lg kt-portlet--responsive-mobile"
                     id="kt_page_portlet">
                    <div class="kt-portlet__head kt-portlet__head--lg" style="">
                        <div class="kt-portlet__head-label">
                            <h3 class="kt-portlet__head-title">Thông tin học viên: {{ $result->name }}
                            </h3>
                        </div>
                       
                    </div>
                </div>
                <!--end::Portlet-->
            </div>
        </div>

        <div class="row">
            <div class="col-xs-12 col-md-8">
                <!--begin::Portlet-->
                <div class="kt-portlet">
                 
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first" style="margin: 0;">
                                @foreach($module['form']['general_tab'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
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

                <div class="kt-portlet">
            
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="log_action">
                                <label>Lưu lịch sử tư vấn</label>
                                <div class="form-group-div form-group col-md-12" style="margin-bottom: 10px;" id="form-group-name">
                                    <div class="col-xs-12">                                            
                                        <input type="text" name="log_name" placeholder="Chủ đề" class="form-control required" >
                                    </div>  
                                </div>
                                <div class="form-group-div form-group col-md-12" id="form-group-name">
                                    <div class="col-xs-12">                                            
                                        <textarea type="text" placeholder="Nội dung tư vấn" name="log_note" class="form-control required" ></textarea>
                                    </div>
                                </div>
                                <div class="form-group-div form-group col-md-12" id="form-group-name">
                                    @if($result->status == 'Thả nổi')
                                        <p style="color: red; font-weight: bold;">Nhớ chuyển trạng thái sang "Đang chăm sóc"</p>
                                    @endif
                                    <button type="button" class="log_submit">Lưu lại</button>
                                </div>
                                <script type="">
                                    $('.log_action .log_submit').click(function() {
                                        if ($('textarea[name=log_note]').val() == '') {
                                            alert('Không được để trống Nội dung tư vấn');
                                        } else {
                                            $.ajax({
                                                url: '/admin/lead/lead-contacted-log',
                                                type: 'POST',
                                                data: {
                                                    title: $('input[name=log_name]').val(),
                                                    note: $('textarea[name=log_note]').val(),
                                                    lead_id: '{{ @$result->id }}',
                                                    type: 'lead',
                                                },
                                                success: function() {
                                                    location.reload();
                                                    // window.location.href = "/admin/lead";
                                                },
                                                error: function() {
                                                    alert('Có lỗi xảy ra. Vui lòng load lại trang và thử lại!');
                                                }
                                            });
                                        }
                                    });
                                </script>
                            </div>

                            <div class="log_logs">
                                <?php 
                            $logs = \App\CRMEdu\Models\LeadContactedLog::where('type', 'lead')->where('lead_id', @$result->id)->orderBy('id', 'desc')->get();
                            ?>
                            @foreach($logs as $log)
                            <hr>
                                <div class="log-item" data-id="{{ $log->id }}" style="color: #000;">
                                    <i></i>
                                    <div class="log-content">
                                        <span><strong>{{ $log->title }}</strong></span>
                                        <p style="font-size: 13px; margin: 0;">{!! $log->note !!}</p>
                                    </div>
                                    <i style="font-size: 11px;">{{ date('H:i d/m/Y', strtotime($log->created_at)) }}   - Bởi: {{ @$log->admin->name }}</i>
                                </div>
                            @endforeach
                            <hr>
                                <div class="log-item" data-id="" style="color: #000;">
                                    <i></i>
                                    <div class="log-content">
                                        <p style="font-size: 13px; margin: 0;">Tạo mới</p>
                                    </div>
                                    <i style="font-size: 11px;">{{ date('H:i d/m/Y', strtotime($result->created_at)) }}   - Bởi: {{ @$result->admin->name }}</i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-12 col-md-4">
                <!--begin::Portlet-->
                <div class="kt-portlet">
            
                    <!--begin::Form-->
                    <div class="kt-form">
                        <div class="kt-portlet__body">
                            <div class="kt-section kt-section--first">
                                @foreach($module['form']['tab_2'] as $field)
                                    @php
                                        $field['value'] = @$result->{$field['name']};
                                    @endphp
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

                                <div class="form-group-div form-group">
                                    <div class="col-xs-12">
                                        <label>Kinh doanh phụ trách:</label>
                                        <?php 
                                            $sales = \App\Models\Admin::select('name', 'tel')->whereIn('id', explode('|', $result->saler_ids))->get();
                                        ?>
                                        <ul>
                                            @foreach($sales as $sale)
                                                <li>{{ $sale->name }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end::Form-->
                </div>

            <!--end::Portlet-->
            </div>

        </div>
    </form>
@endsection
@section('custom_head')
    <link type="text/css" rel="stylesheet" charset="UTF-8"
          href="{{ asset(config('core.admin_asset').'/css/form.css') }}">

<style type="">
    .form-group-div p {
        font-size: 13px;
    }
</style>
@endsection
@section('custom_footer')
    <script src="{{ asset(config('core.admin_asset').'/js/pages/crud/metronic-datatable/advanced/vertical.js') }}"
            type="text/javascript"></script>

    <script type="text/javascript" src="{{ asset('public/tinymce/tinymce.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('public/tinymce/tinymce_editor.js') }}"></script>
    <script type="text/javascript">
        editor_config.selector = ".editor";
        editor_config.path_absolute = "{{ (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]" }}/";
        tinymce.init(editor_config);
    </script>
    <script type="text/javascript" src="{{ asset(config('core.admin_asset').'/js/form.js') }}"></script>
@endsection
