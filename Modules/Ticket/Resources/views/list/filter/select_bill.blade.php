<div class="col-sm-6 col-lg-3 kt-margin-b-10-tablet-and-mobile list-filter-item">
    <label>{{ @$field['label'] }}:</label>
    <select name="{{ $name }}" class="form-control kt-input {{ @$field['class'] }}">
        @php
            $model = new $field['model'];
            if(isset($field['where']))
                $model = $model->whereRaw($field['where']);
            $data = $model->where(function ($query) {
                        $query->orWhere('customer_id', \Auth::guard('admin')->user()->id);
                        $query->orWhere('curator_ids', 'LIKE', '%|' . \Auth::guard('admin')->user()->id . '|%');
                    })->orderBy($field['display_field'], 'asc')->pluck($field['display_field'], 'id');
        @endphp
        <option value=""></option>
        @foreach ($data as $k => $v)
            <option value="{{ $k }}" {{ ((isset($_GET[$name]) && @$_GET[$name] == $k) || @$field['value'] ==  @$k) ? 'selected' : '' }}>{{ $v }}</option>
        @endforeach
    </select>
</div>