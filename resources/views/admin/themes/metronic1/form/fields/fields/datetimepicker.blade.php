
<div class='input-group date' id='date-{{ $field['name'] }}'>
    <?php $time = isset($field['value']) ? strtotime($field['value']) : time();?>
    <input
            @if(@$field['value'] != null)
            value="{{ old($field['name']) != null ? old($field['name']) : @date(isset($field['date_format']) ? $field['date_format'] : 'd-m-Y', $time) }}"
            @endif
            type='text' name="{{ $field['name'] }}" {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
            class="form-control datetimepicker" {!! @$field['inner'] !!}/>
    <span class="input-group-addon select-datetimepicker">
                        <span class="glyphicon glyphicon-calendar"></span>
            </span>
</div>
<script>
    $(document).ready(function () {
        $('#date-{{ $field['name'] }}').datepicker({format: "{{isset($field['format']) ? $field['format'] : 'dd-mm-yyyy'}}",});
    });
</script>