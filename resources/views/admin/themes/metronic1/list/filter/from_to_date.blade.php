<div>
    <div style="width: 50%; float: left;">
        <input name="from_date" class="form-control kt-input" placeholder="Từ ngày" type="date"
               style="padding: 6px 1px;"
               value="{{ @$_GET['from_date'] }}" title="Từ ngày">
    </div>
    <div style="width: 50%; float: left;">
        <input name="to_date" class="form-control kt-input" placeholder="Đế ngày" type="date"
               style="padding: 6px 1px;"
               value="{{ @$_GET['to_date'] }}" title="Đến ngày">
    </div>
    <input type="hidden" name="{{ $name }}" value="1">
</div>