<!-- Modal -->
<div id="updateGoalModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Update <span class="modal_worker_name"></span> goals</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="/sales/update-goal" id="update_goal_form">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="daily">Daily</label>
                        <input type="text" name="daily" class="form-control" id="daily" value="" required>
                    </div>
                    <div class="form-group">
                        <label for="monthly">Monthly</label>
                        <input type="text" name="monthly" class="form-control" id="monthly" value="" required>
                        <input type="hidden" name="employee_id" id="goal_employee_id" value="">
                    </div>
                    <input type="submit" value="UPDATE GOAL" class="btn btn-success">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>