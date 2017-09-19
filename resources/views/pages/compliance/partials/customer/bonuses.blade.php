<div class="panel panel-primary">
    <div class="panel-heading">Bonuses</div>
    <div class="panel-body">
        <table id="bonuses_table" class="display" cellspacing="0" width="100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Method</th>
                    <th>Cleared By</th>
                    <th>Amount</th>
                    <th>Confirmed at</th>
                </tr>
            </tfoot>
            <tbody>
                @if( !$customer->bonuses->count())
                    <tr>
                        <td colspan="5" class="text-center">No Bonuses Available</td>
                    </tr>
                @else
                    @foreach($customer->bonuses as $bonus)
                    <tr>
                        <td>{{ $bonus->id }}</td>
                        <td>{{ $bonus->paymentMethod }}</td>
                        <td>{{ $bonus->clearedBy }}</td>
                        <td>{{ number_format($bonus->amount) }}</td>
                        <td>{{ $bonus->confirmTime->format('m-d-Y H:i:s') }}</td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>