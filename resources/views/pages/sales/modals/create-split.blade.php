<!-- Modal -->
<div id="createSplitModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Split with an employee</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="/sales/create-new-split" id="split_form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="split_employee_id">Other Employee</label>
                        <select name="split_employee_id" id="split_employee_id" class="nice-select form-control" style="width: 100%" required>
                            <option>Select an employee</option>
                            @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount">Amount <span style="font-size: 12px;">(This amount will be taken from this employee)</span></label>
                        <input type="text" name="amount" class="form-control" id="amount" value="" required>
                        <input type="hidden" name="deposit_employee_id" id="deposit_employee_id" value="">
                        <input type="hidden" name="deposit_amount" id="deposit_amount" value="">
                    </div>
                    <input type="submit" value="SPLIT DEPOSIT" class="btn btn-success">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>