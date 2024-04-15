<div class="form-group-div form-group " id="form-group-links">
    <label for="links">Tên trang web</label>
    <div class="col-xs-12">
        <?php
        $field['value'] = isset($result->domain_id) ? $result->domain_id : @$result->id;
        $domains = \Modules\CheckErrorLink\Models\DomainCheck::pluck('name', 'id')->toArray();
        ?>
        <select class="form-control {{ $field['class'] or '' }}" id="{{ $field['name'] }}"
                name="{{ $field['name'] }}">
            @foreach ($domains as $value => $name)
                <option value='{{ $value }}' {{ $value == $field['value'] ? 'selected' : '' }}>{{ trans($name) }}</option>
            @endforeach
        </select>
    </div>
</div>
