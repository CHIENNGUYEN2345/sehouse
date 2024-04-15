
    <label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
            <span class="color_btd">*</span>@endif</label>
    <div class="col-xs-12">
        <?php 
$date1=date_create(@$result->dating);
$date2=date_create(date('Y-m-d', strtotime('+5 days')));
$diff=date_diff($date1,$date2);
dd($diff->d);
        ?>

    </div>
