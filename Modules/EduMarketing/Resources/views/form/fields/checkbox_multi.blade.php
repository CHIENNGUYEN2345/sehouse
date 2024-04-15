<label style="cursor: pointer" for="Đối tượng gửi mail">Đối tượng nhận mail
<div class="kt-checkbox-list mt-3">
    <?php
    $objects=explode( '|', @$result->object);
    ?>
    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
        <input type="checkbox" name="{{ $field['name'] }}[]" value="student" @if(in_array("student", $objects))checked @endif> Học viên
        <span></span>
    </label>
    <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
        <input type="checkbox" name="{{ $field['name'] }}[]" value="lecturer" @if(in_array('lecturer', $objects))checked @endif> Giáo viên
        <span></span>
    </label>
   <label class="kt-checkbox kt-checkbox--tick kt-checkbox--brand">
        <input type="checkbox" name="{{ $field['name'] }}[]"  value="customer" @if(in_array('customer', $objects))checked @endif> Khách hàng
        <span></span>
    </label>
</div>
</label>