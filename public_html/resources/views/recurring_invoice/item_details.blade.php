<tbody data-repeater-item>
    <tr>
        <td>
            {{ Form::text('items[__REPLACE_INDEX__][description]', $product->name . ' - ' . @$assign_room[0]->space->name, ['class' => 'form-control', 'required' => 'required']) }}
        </td>
        <td>
            {{ Form::number('items[__REPLACE_INDEX__][quantity]', $assign_room->count('chair_id'), ['class' => 'form-control quantity', 'required' => 'required', 'min' => 1]) }}
        </td>
        <td>
            {{ Form::number('items[__REPLACE_INDEX__][price]', $contract_data->value / ($assign_room->count('chair_id') ?: 1), ['class' => 'form-control price', 'required' => 'required', 'step' => '0.01']) }}
        </td>
        <td>
            <select name="items[__REPLACE_INDEX__][tax][]" class="form-control select tax-select">
                @foreach($taxes_data as $tax)
                    <option value="{{$tax['id']}}" data-rate="{{$tax['rate']}}" selected>{{$tax['name']}} ({{$tax['rate']}}%)</option>
                @endforeach
            </select>
        </td>
        <td>
            {{ Form::number('items[__REPLACE_INDEX__][discount]', 0, ['class' => 'form-control discount', 'step' => '0.01']) }}
        </td>
        <td class="text-end amount">0.00</td>
        <td><a href="#" class="ti ti-trash text-danger" data-repeater-delete></a></td>
    </tr>
</tbody>
