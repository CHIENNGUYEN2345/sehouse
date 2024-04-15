<div class="kt-portlet">
    <div class="kt-portlet__head">
        <div class="kt-portlet__head-label">
            <h3 class="kt-portlet__head-title">
                Thông tin thanh toán
            </h3>
        </div>
    </div>
    <!--begin::Form-->
    <div class="kt-form">
        <div class="kt-portlet__body">
            <div class="kt-section kt-section--first">
                <!-- <?php
                $field = ['name' => 'finance_total', 'type' => 'price_vi', 'label' => 'Tổng tiền', 'class' => ' required', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_finance->total : 0];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                <span class="color_btd">*</span>@endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div> -->

                <!-- <?php 
                $field = ['name' => 'finance_received', 'type' => 'price_vi', 'label' => 'Đã trả', 'class' => ' required', 'group_class' => 'col-md-3', 'value' => isset($result) ? @$result->bill_finance->received : 0];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                <span class="color_btd">*</span>@endif</label>
                    <div class="col-xs-12">
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                        <span class="text-danger">{{ $errors->first($field['name']) }}</span>
                    </div>
                </div> -->

                @if(isset($result))
                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                         id="form-group-{{ $field['name'] }}">
                        <label for="{{ $field['name'] }}">Đã trả</label>
                        <div class="col-xs-12">
                            {{ number_format(@$result->total_received, 0, ',', '.') }}<sup>đ</sup>
                        </div>
                    </div>

                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                         id="form-group-{{ $field['name'] }}">
                        <label for="{{ $field['name'] }}">Còn thiếu</label>
                        <div class="col-xs-12">
                            {{ number_format(@$result->total_price_contract - @$result->total_received, 0, ',', '.') }}<sup>đ</sup>
                        </div>
                    </div>

                    <?php
                    $field = ['name' => 'iframe', 'type' => 'iframe', 'class' => 'col-xs-12 col-md-12 padding-left'
                        , 'src' => '/admin/bill_receipts?bill_id={id}', 'inner' => 'style="min-height: 400px;"'];
                    ?>
                    <div class="form-group-div form-group {{ @$field['group_class'] }}"
                                             id="form-group-{{ $field['name'] }}">
                         <label for="{{ $field['name'] }}" style="font-size: 1.2rem;font-weight: bold;color: #48465b;">Phiếu thu</label>
                        @include(config('core.admin_theme').".form.fields.".$field['type'], ['field' => $field])
                    </div>
                @endif
                

                <?php 
                $field = ['name' => 'finance_detail', 'type' => 'textarea', 'label' => 'Chi tiết hạng mục tiền', 'class' => '', 'group_class' => 'col-md-12', 'value' => isset($result) ? @$result->bill_finance->detail : '', 'inner' => 'placeholder="Ví dụ: Gói chuyên nghiệp 2 năm tặng 1 năm 6,3tr. Cọc trước 100% tặng thêm 6 tháng. Tên miền .vn 670k"'];
                ?>
                <div class="form-group-div form-group {{ @$field['group_class'] }}"
                     id="form-group-{{ $field['name'] }}">
                    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
                                <span class="color_btd">*</span>@endif</label>
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