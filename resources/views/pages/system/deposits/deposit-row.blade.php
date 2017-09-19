<tr>
    <td>{{ $deposit->id }}</td>
    <td><a target="_blank" href="https://spotcrm.ivoryoption.com/crm/customers/page/{{ $deposit->customerId }}">{{ $deposit->customerId }}</a></td>
    <td>{{ $currencies[$deposit->currency] . number_format($deposit->amount) . " (" . $deposit->currency . ")" }}</td>
    <td>{{ strlen($deposit->transactionID) > 10 ? substr($deposit->transactionID, 0, 10) . "\n..."  : $deposit->transactionID}}</td>
    <td>{{ $deposit->paymentMethod }}</td>
    <td>{{ $deposit->clearedBy }}</td>
    <td>
        <div class="radio col-md-6">
            <label>
                <input type="radio" data-deposit-id="{{$deposit->id}}" class="is_verified_radio" name="is_verified_{{$deposit->id}}" value="1" @if($deposit->is_verified) checked @endif>
                YES
            </label>
        </div>
        <div class="radio col-md-6" style="margin-top:10px;">
            <label>
                <input type="radio" data-deposit-id="{{$deposit->id}}" class="is_verified_radio" name="is_verified_{{$deposit->id}}" value="0" @if(!$deposit->is_verified) checked @endif>
                NO
            </label>
        </div>
    </td>
    <td>
        @if($deposit->receptionEmployeeId == 0)
            "Selfie"
        @else
            @if($deposit->employee)
                {{ $deposit->employee->name }}
            @else
                "Deleted employee"
            @endif
        @endif
    </td>
    <td>
        <div class="radio col-md-6">
            <label>
                <input type="radio" data-deposit-id="{{$deposit->id}}" class="deposit_type_radio" name="deposit_type_{{$deposit->id}}" value="1" @if($deposit->deposit_type == 1) checked @endif>
                FTD
            </label>
        </div>
        <div class="radio col-md-6" style="margin-top:10px;">
            <label>
                <input type="radio" data-deposit-id="{{$deposit->id}}" class="deposit_type_radio" name="deposit_type_{{$deposit->id}}" value="2" @if($deposit->deposit_type == 2) checked @endif>
                RST
            </label>
        </div>
    </td>
    <td>
        {{ $deposit->table_id ? $deposit->table->name : "No table" }}
    </td>
    <td>{{ $deposit->confirmTime ? $deposit->confirmTime->format('d/m/Y H:i') : "N/A" }}</td>
    <td>
        @if($deposit->is_split && isset($deposit->split->employee))
            Split with : {{ $deposit->split->employee->name }}<br/>
            For : {{ $currencies[$deposit->currency] . number_format($deposit->split->amount) }} 
            <button class="btn btn-xs btn-danger remove_split" data-deposit-id="{{ $deposit->id }}" data-split-id="{{ $deposit->split->id }}" data-toggle="tooltip" data-placement="bottom" title="Remove split with {{ $deposit->split->employee->name }}"><i class="fa fa-trash-o"></i></button>
        @endif
    </td>
    <td>
        <!-- <button class="btn btn-xs btn-success split_deposit" data-deposit-amount="{{ $deposit->amount }}" data-deposit-id="{{ $deposit->id }}" @if( !$deposit->receptionEmployeeId || $deposit->is_split) style="display:none;" @endif>SPLIT</button> -->
    </td>
</tr>