<?php 
$admin_ids = \App\Models\RoleAdmin::where(function ($query) {
                    $query->orWhere('role_id', 178);       //  điều hành dự án
                    $query->orWhere('role_id', 173);        //  kỹ thuật
                    $query->orWhere('role_id', 188);        //  kỹ thuật
                })
                    ->pluck('admin_id')->toArray();
$admins = \App\Models\Admin::select('id', 'name', 'tel')->whereIn('id', $admin_ids)->where('status', 1)->get();
?>

<select class="form-control select2-dieu_hanh {{ $field['class'] or '' }}" id="{{ $field['name'] }}" {!! @$field['inner'] !!}
{{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
name="{{ $field['name'] }}@if(isset($field['multiple'])){{ '[]' }}@endif"
@if(isset($field['multiple'])) multiple @endif>
    <option value="0">-</option>
    @foreach ($admins as $admin)
    <option value='{{ $admin->id }}' {{ $field['value'] == $admin->id ? 'selected' : '' }}>{{ $admin->name }} | {{ $admin->tel }}</option>
    @endforeach
</select>
<script>
    $(document).ready(function () {
        $('.select2-dieu_hanh').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
        });
    });
</script> 