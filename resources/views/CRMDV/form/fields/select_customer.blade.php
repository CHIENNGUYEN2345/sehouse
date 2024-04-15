<?php

$value = $data = [];
if (isset($_GET['admin_id'])) {
    $value = [$_GET['admin_id']];
}

if (isset($field['multiple']) && isset($field['value'])) {
    if (is_array($field['value']) || is_object($field['value'])) {
        foreach ($field['value'] as $item) {
            $value[] = $item->id;

        }
    } elseif (is_string($field['value'])) {
        $value = explode('|', $field['value']);
    }
} else {
    if (old($field['name']) != null) {
        $value[] = old($field['name']);
    } elseif (isset($field['value'])) { //  nếu set sẵn giá trị khách thì chọn
        $value[] = $field['value'];
    } elseif (isset($_GET[$field['name']])) {   //  nếu truyền tham số giá trị khách trên url thì chọn
        $value[] = $_GET[$field['name']];
    }
}

$model = new $field['model'];
if (!empty($value)) {
    $orderRaw = 'CASE ';
    foreach ($value as $k => $v) {
        if ($v != '') {
            $orderRaw .= " WHEN id = " . $v . " THEN " . $k;
        }
    }
    $orderRaw .= ' ELSE id END ASC';
    $data = $model->whereIn('id', $value);
    // $data = $data->whereNotIn('id', $not_customer);
    if ($orderRaw != 'CASE  ELSE id END ASC') {
        $data = $data->orderByRaw($orderRaw);
    }
    $data = $data->get();
}
?>
<style>
    .select2-container--default .select2-results__option[aria-selected=true] {
        background: #71F39E !important;
        color: #fff !important;
    }
</style>
<label for="{{ $field['name'] }}">{{ @$field['label'] }} @if(strpos(@$field['class'], 'require') !== false)
        <span class="color_btd">*</span>
    @endif</label>
<select style="width: 100%" type="select2" class="form-control {{ $field['class'] or '' }} select2-{{ $field['name'] }}"
        id="{{ $field['name'] }}"
        {{ strpos(@$field['class'], 'require') !== false ? 'required' : '' }}
        name="{{ $field['name'] }}{{ isset($field['multiple']) ? '[]' : '' }}" {{ isset($field['multiple']) ? 'multiple' : '' }} {!! @$field['inner'] !!}>
    <option value="">Chọn {{ trans(@$field['label']) }}</option>

    @if(!empty($data))
        @foreach ($data as $v)
            <option selected
                    value="{{ $v->id }}">{{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}</option>
        @endforeach
    @endif
</select>
<script>
    $(document).ready(function () {
        $('.select2-{{ $field['name'] }}').select2({
            @if(isset($field['multiple']))
            closeOnSelect: false,
            @endif
            ajax: {
                url: "/admin/{{ $field['object'] }}/search-for-select2",
                dataType: 'json',
                data: function (params) {
                    return {
                        keyword: params.term, // search term
                        col: '{{ $field['display_field'] }}',
                        col2: '{{ @$field['display_field2'] }}',
                        where: '{{ @$field['where'] }}',
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            title: 'Chọn {{ @$field['label'] }}',
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });

        function formatRepo(repo) {
            if (repo.loading) {
                return repo.text;
            }

            var markup = "<div class='select2-result-repository clearfix'>" + repo.{{ $field['display_field'] }};
            @if(isset($field['display_field2']))
            if (repo.{{ $field['display_field2'] }} != null) {
                markup += ' | ' + repo.{{ $field['display_field2'] }};
            } else {
                markup += "</div></div>";
            }
            @endif
                markup += "</div></div>";
            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.{{ $field['display_field'] }} || repo.text;
        }

        @if(empty($data))
        $('#select2-{{ $field['name'] }}-container').html('Chọn {{ trans(@$field['label']) }}');
        @else
        @foreach ($data as $v)
        $('#select2-{{ $field['name'] }}-container').html('{{ $v->{$field['display_field']} }}{{ isset($field['display_field2']) ? ' | ' . $v->{$field['display_field2']} : '' }}');
        @endforeach
        @endif
    });
</script>

@include('CRMDV.partials.customer_info', ['field_name' => @$field['name']])
