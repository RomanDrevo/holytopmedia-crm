<div class="panel panel-primary">
    <div class="panel-heading">{{ $customer->name() }} information</div>
    <div class="panel-body">
        <table class="table table-striped jambo_table bulk_action">

            <tr>
                <td>Name</td>
                <td>{{ $customer->name() }}</td>
            </tr>
            <tr>
                <td>Email</td>
                <td>{{ $customer->email }}</td>
                <input type="hidden" name="primary_email" id="primary_email" value="{{ $customer->email }}" />
            </tr>
            <tr>
                <td>Secondary Email</td>
                <td>
                    <input type="email" name="secondary_email" id="secondary_email" data-email="{{ $customer->secondary_email }}" class="form-control" value="{{ $customer->secondary_email }}" />
                </td>
            </tr>
            <tr>
                <td>Phone</td>
                <td>{{ $customer->Phone }}</td>
            </tr>
            <tr>
                <td>Secondary Phone</td>
                <td>
                    <input type="text" name="secondary_phone" id="secondary_phone" class="form-control" value="{{ $customer->secondary_phone }}" />
                </td>
            </tr>
            <tr>
                <td>Country</td>
                <td>{{ $countries[$customer->Country] }}</td>
            </tr>
            <tr>
                <td>Verification Status</td>
                <td>
                    <select name="verification" id="verification" class="form-control">
                        <option value="None" {{ strtolower($customer->verification) == 'none' ? 'selected' : '' }}>None</option>
                        <option value="Partial" {{ strtolower($customer->verification) == 'partial' ? 'selected' : '' }}>Partial</option>
                        <option value="Full" {{ strtolower($customer->verification) == 'full' ? 'selected' : '' }}>Full</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Campaign ID</td>
                <td>{{ $customer->campaignId }}</td>
            </tr>
            <tr>
                <td>Currency</td>
                <td>{{ $customer->currency }}</td>
            </tr>
            <tr>
                <td>Registration Status</td>
                <td>{{ $customer->regStatus }}</td>
            </tr>
            <tr>
                <td>First Deposit Date</td>
                <td>{{ $customer->firstDepositDate ? $customer->firstDepositDate->format('m-d-Y H:i:s') : "No Deposits"}}</td>
            </tr>
            <tr>
                <td>Last Deposit Date</td>
                <td>{{ $customer->lastDepositDate ? $customer->lastDepositDate->format('m-d-Y H:i:s') : "No Deposits" }}</td>
            </tr>
            <tr>
                <td>Last Withdrawal Date</td>
                <td>
                    {{ $customer->lastWithdrawalDate ? $customer->lastWithdrawalDate->format('m-d-Y H:i:s') : "No Withdrawals"}}
                </td>
            </tr>
            <tr>
                <td>Last Login Date</td>
                <td>{{ $customer->lastLoginDate ? $customer->lastLoginDate->format('m-d-Y H:i:s') : "No data"}}</td>
            </tr>
            <tr>
                <td>Total Deposits</td>
                    <td>{{ $currencies[$customer->currency] }} {{ number_format($totalDeposits)}}</td>
            </tr>
            <tr>
                <td>Total CC Deposits</td>
                <td>{{ $currencies [$customer->currency] }} {{ number_format($totalCCdeposits)}}</td>
            </tr>
        </table>
    </div>
</div>

{{--test--}}