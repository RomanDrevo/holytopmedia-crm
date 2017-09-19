<tr>
    <td>{{ $deposit->id }}</td>
    <td>{{ $deposit->customerId }}</td>
    <td>
        {{ $deposit->customer ? $deposit->customer->name() : "Not Available" }}
    </td>
    <td>{{ $currencies[$deposit->currency] . number_format($deposit->amount) . " (" . $deposit->currency . ")" }}</td>
    <td>{{ strlen($deposit->transactionID) > 10 ? substr($deposit->transactionID, 0, 10) . "\n..."  : $deposit->transactionID}}</td>
    <td>{{ $deposit->paymentMethod }}</td>
    <td>{{ $deposit->clearedBy }}</td>
    <td>
        {{ $deposit->customer ? $deposit->customer->verification : "Not Available" }}
    </td>
    <td>{{ ($deposit->approved) ? 'YES' : 'NO'}}</td>
    <td>
        @if($deposit->receptionEmployeeId == 0)
            {!! str_replace("[deposit_id]", $deposit->id, $employeesSelect) !!}
        @else
            {!! str_replace(['[deposit_id]', 'option value="'.$deposit->receptionEmployeeId.'"'], [$deposit->id, 'option value="'.$deposit->receptionEmployeeId.'" selected'], $employeesSelect) !!}
        @endif
    </td>
    <td>
        <div class="radio col-md-6">
            <label>
                <input type="radio" data-deposit-id="{{$deposit->id}}" class="deposit_type_radio"
                       name="deposit_type_{{$deposit->id}}" value="1" @if($deposit->deposit_type == 1) checked @endif>
                FTD
            </label>
        </div>
        <div class="radio col-md-6" style="margin-top:10px;">
            <label>
                <input type="radio" data-deposit-id="{{$deposit->id}}" class="deposit_type_radio"
                       name="deposit_type_{{$deposit->id}}" value="2" @if($deposit->deposit_type == 2) checked @endif>
                RST
            </label>
        </div>
    </td>
    <td>
        <select data-deposit-id="{{$deposit->id}}" class="deposit-table-selector nice-select form-control"
                style="width: 100%">
            <option>No table selected</option>
            @foreach($tables as $table)
                <option value="{{ $table->id }}"
                        @if($deposit->table_id == $table->id) selected @endif>{{ $table->name }}</option>
            @endforeach
        </select>
    </td>
    <td>{{ ($deposit->confirmTime) ? $deposit->confirmTime->format('m-d-Y H:i') : "N/A"}}</td>
    <td>
        @if($deposit->is_split && isset($deposit->split->employee))
            Split with : {{ $deposit->split->employee->name }}<br/>
            For : {{ $currencies[$deposit->currency] . number_format($deposit->split->amount) }}
            <button class="btn btn-xs btn-danger remove_split" data-deposit-id="{{ $deposit->id }}"
                    data-split-id="{{ $deposit->split->id }}" data-toggle="tooltip" data-placement="bottom"
                    title="Remove split with {{ $deposit->split->employee->name }}"><i class="fa fa-trash-o"></i>
            </button>
        @endif
        {{ $deposit->note }}
    </td>
    <td style="text-align: center;">
        <button class="btn btn-xs btn-success split_deposit deposit_actions" data-deposit-amount="{{ $deposit->amount }}"
                data-deposit-id="{{ $deposit->id }}"
                @if( !$deposit->receptionEmployeeId || $deposit->is_split) style="display:none;" @endif>SPLIT
        </button>
        <button class="btn btn-xs btn-success deposit_note deposit_actions add_note" data-toggle="modal" data-target=".notes-modal-sm" data-deposit-id="{{ $deposit->id }}">ADD NOTE</button>
    </td>
</tr>