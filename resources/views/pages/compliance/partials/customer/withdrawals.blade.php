<div class="panel panel-primary">
    <div class="panel-heading">Withdrawals</div>
    <div class="panel-body">
        <table id="withdrawals_table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Requested at</th>
                    <th>Confirmed at</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Requested at</th>
                    <th>Confirmed at</th>
                </tr>
            </tfoot>
            <tbody>
                @if(count($customer->withdrawals) < 1)
                    <tr>
                        <td colspan="5" class="text-center">No Withdrawals Available</td>
                    </tr>
                @else
                    @foreach($customer->withdrawals as $withdrawal)
                    <tr>
                        <td>{{ $withdrawal->id }}</td>
                        <td>{{ $withdrawal->paymentMethod }}</td>
                        <td>{{ number_format($withdrawal->amount) }}</td>
                        <td>{{ $withdrawal->requestTime->format('m-d-Y H:i:s') }}</td>
                        <td>{{ $withdrawal->confirmTime->format('m-d-Y H:i:s') }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>