<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Triển khai
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <?php
                $field = ['name' => 'progress_status', 'type' => 'select', 'label' => 'Tiến độ', 'class' => ' required', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_progress->status : '', 'options' => [
                    '' => '',
                    'Thu thập YCTK L1' => 'Thu thập YCTK L1',
                    'Triển khai L1' => 'Triển khai L1',
                    'Nghiệm thu L1 & thu thập YCTK L2' => 'Nghiệm thu L1 & thu thập YCTK L2',
                    'Triển khai L2' => 'Triển khai L2',
                    'Nghiệm thu L2 & thu thập YCTK L3' => 'Nghiệm thu L2 & thu thập YCTK L3',
                    'Triển khai L3' => 'Triển khai L3',
                    'Nghiệm thu L3 & thu thập YCTK L4' => 'Nghiệm thu L3 & thu thập YCTK L4',
                    'Triển khai L4' => 'Triển khai L4',
                    'Nghiệm thu L4 & thu thập YCTK L5' => 'Nghiệm thu L4 & thu thập YCTK L5',
                    'Triển khai L5' => 'Triển khai L5',
                    'Nghiệm thu L5 & thu thập YCTK L6' => 'Nghiệm thu L5 & thu thập YCTK L6',
                    'Triển khai L6' => 'Triển khai L6',
                    'Chờ khách xác nhận' => 'Chờ khách xác nhận',
                    'Khách xác nhận xong' => 'Khách xác nhận xong',
                    'Kết thúc' => 'Kết thúc',
                    'Tạm dừng' => 'Tạm dừng',
                    'Bỏ' => 'Bỏ',
                ]];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>

                <?php
                $field = ['name' => 'progress_rate_content', 'type' => 'text', 'label' => 'Đánh giá dự án', 'class' => '', 'group_class' => 'col-md-9 required', 'value' => isset($result) ? @$result->bill_progress->rate_content : '', ];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>

                <?php
                $field = ['name' => 'customer_classify', 'type' => 'select', 'label' => 'Phân loại khách', 'class' => ' required', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->user->classify : '', 'options' => [
                    '-' => '',
                    'Hiểu web, dễ tính' => 'Hiểu web, dễ tính',
                    'Hiểu web, khó tính' => 'Hiểu web, khó tính',
                    'Không hiểu web, dễ tính' => 'Không hiểu web, dễ tính',
                    'Không hiểu web, khó tính' => 'Không hiểu web, khó tính',

                ]];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>

                <?php
                $field = ['name' => 'progress_dh_id', 'type' => 'custom', 'label' => 'NV điều hành', 'class' => 'required', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_progress->dh_id : '',];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include('CRMDV.form.partials.select_dieu_hanh', ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>

                <?php
                $field = ['name' => 'progress_kt_id', 'type' => 'custom', 'label' => 'NV kỹ thuật', 'class' => 'required', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_progress->kt_id : '',];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include('CRMDV.form.partials.select_ky_thuat', ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>

                <?php
                $field = ['name' => 'progress_reminder_customer', 'type' => 'date', 'label' => 'Ngày hẹn deadline', 'class' => '', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_progress->reminder_customer : '',];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>


                <?php
                $field = ['name' => 'kh_xong_image', 'type' => 'file_image', 'label' => 'Ảnh bằng chứng Khách xác nhận xong', 'class' => '', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_progress->kh_xong_image : '',];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>
            </div>

            @include("CRMDV.dhbill.partials.lich_su_trang_thai")
        </div>
    </div>
    <!--end::Form-->
</div>


<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Đánh giá triển khai
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">

                <?php
                $field = ['name' => 'progress_rate', 'type' => 'select', 'label' => 'Đánh giá', 'class' => ' ', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_progress->rate : '', 'options' => [
                    '-' => '',
                    1 => '*',
                    2 => '* *',
                    3 => '* * *',
                    4 => '* * * *',
                    5 => '* * * * *',
                ], 'inner' => 'disabled'];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>

                <?php
                $field = ['name' => 'progress_rate_content', 'type' => 'textarea', 'label' => 'Chi tiết đánh giá', 'class' => '', 'group_class' => 'col-md-12', 'value' => isset($result) ? @$result->bill_progress->rate_content : '', 'inner' => 'disabled'];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                            <span class="color_btd">*</span>
                        @endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!--end::Form-->
</div>