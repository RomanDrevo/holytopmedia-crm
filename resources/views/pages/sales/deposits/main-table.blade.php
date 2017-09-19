<table class="table table-striped table-bordered table-hover" id="dataTables-deposits">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer ID</th>
            <th>Customer Name</th>
            <th>Amount</th>
            <th>Transaction ID</th>
            <th>Payment Method</th>
            <th>Cleared By</th>
            <th>Verification</th>
            <th>Is Approved</th>
            <th>Employee</th>
            <th>Type</th>
            <th>Table on assignment</th>
            <th>Confirm Time</th>
            <th>Notes</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($deposits as $deposit)
            @include('pages.sales.deposits.deposit-row')
        @endforeach
    </tbody>
</table>