<div class="panel panel-primary">
    <div class="panel-heading">Deposits (Total: {{ Config::get('liantech.currencies_symbols')[$customer->currency] }} {{ number_format($totalDeposits)}})</div>
    <div class="panel-body">
        <table id="deposits_table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                    <th>Approved?</th>
                    <th>Approve Deposit</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                    <th>Approved?</th>
                    <th>Approve Deposit</th>
                </tr>
            </tfoot>
            <tbody>
                @if(count($customer->deposits) < 1)
                    <tr>
                        <td colspan="7" class="text-center">No Deposits Available</td>
                    </tr>
                @else
                    @foreach($customer->deposits as $deposit)
                    <tr>
                        <td>{{ $deposit->id }}</td>
                        <td>{{ $deposit->paymentMethod }}</td>
                        <td>{{ $deposit->clearedBy }}</td>
                        <td>{{ number_format($deposit->amount) }}</td>
                        <td>{{ $deposit->confirmTime->format('m-d-Y H:i:s') }}</td>
                        <td>{{ $deposit->approved ? 'YES' : 'NO' }}</td>
                        <td>
                            <button class="approve_deposit btn btn-sm {{ $deposit->approved ? 'btn-danger' : 'btn-success' }}"  data-deposit-id="{{ $deposit->id }}" value="{{ $deposit->id }}">
                                {!! $deposit->approved ? '<i class="fa fa-times"></i>' : '<i class="fa fa-check"></i>' !!}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
        @if(count($customer->deposits))
        <p class="pull-right">
            <!-- <button class="btn btn-primary btn-xs" id="send_dod">
                SEND DOD
            </button> -->
        </p>
        @endif
    </div>
</div>