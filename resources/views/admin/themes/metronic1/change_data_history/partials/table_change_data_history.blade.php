<?php
if (!isset($tables)) {
    $tables = [$module['table_name']];
}
$changeDataHistory = \App\Models\ChangeDataHistory::whereIn('table_name', $tables)->where('item_id', $result->id)->orderBy('id', 'desc')->paginate(10);
$fields_history = [];
foreach ($module['form'] as $tab) {
    foreach ($tab as $field) {
        if (isset($field['type_history'])) {
            $field['type'] = $field['type_history'];
        } elseif (in_array($field['type'], ['select2_ajax_model'])) {
            if (isset($field['multiple'])) {
                $field['type'] = 'relation';
            } else {
                $field['type'] = 'relation_multiple';
            }
        } elseif (in_array($field['type'], ['datetime-local'])) {
            $field['type'] = 'text';
        } elseif (in_array($field['type'], ['custom', 'iframe', 'textarea'])) {
            $field['type'] = 'text';
        } elseif (in_array($field['type'], ['file_editor'])) {
            $field['type'] = 'image';
        }
        $fields_history[$field['name']] = $field;
    }
}
?>
<table class="table table-striped">
    <thead class="kt-datatable__head">
    <tr class="kt-datatable__row" style="left: 0px;">
        <th data-field="admin_id" class="kt-datatable__cell kt-datatable__cell--sort ">
            Người thực hiện
        </th>
        <th data-field="data" class="kt-datatable__cell kt-datatable__cell--sort ">
            Hành động
        </th>
        <th>Ghi chú</th>
        <th data-field="created_at" class="kt-datatable__cell kt-datatable__cell--sort ">
            Thời gian
        </th>
    </tr>
    </thead>
    <tbody class="kt-datatable__body ps ps--active-y" style="max-height: 496px;">
    @foreach($changeDataHistory as $dataHistory)
        <?php
        $column_change = json_decode($dataHistory->data);
        ?>
        <tr data-row="0" class="kt-datatable__row" style="left: 0px;">
            <td data-field="admin_id" class="kt-datatable__cell item-admin_id">
                <a href="/admin/profile/{{ $dataHistory->admin_id }}" target="_blank">{{ @$dataHistory->admin->name }}
                </a>
            </td>
            <td data-field="data" class="kt-datatable__cell item-data">
                @if(is_array($column_change))
                    @foreach($column_change as $column)
                        @if(isset($fields_history[$column->column]['type']))
                            <strong>{{ $fields_history[$column->column]['label'] }}</strong>:
                            <?php
                            try {
                                unset($old_value);
                            } catch (Exception $ex) {

                            }
                            $old_value = $result;
                            $old_value->{$column->column} = $column->old_value;
                            ?>
                            @include(config('core.admin_theme').'.list.td.'.$fields_history[$column->column]['type'], ['item' => $old_value, 'field' => $fields_history[$column->column]])
                            =>
                            <?php
                            $new_value = $result;
                            $new_value->{$column->column} = $column->new_value;
                            ?>
                            @include(config('core.admin_theme').'.list.td.'.$fields_history[$column->column]['type'], ['item' => $new_value, 'field' => $fields_history[$column->column]])
                            <br>
                        @endif
                    @endforeach
                @endif
            </td>
            <td>
                {!! $dataHistory->note !!}
            </td>
            <td data-field="created_at" class="kt-datatable__cell item-created_at">
                {{ date('d/m/Y H:i:s', strtotime($dataHistory->created_at)) }}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>


