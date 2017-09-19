<!-- Modal -->
<div id="createTableModal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Create a new table</h4>
            </div>
            <div class="modal-body">
                <form method="POST" action="/sales/create-new-table">
                    {{ csrf_field() }}
                    <div class="form-group">
                        <label for="type">Table Type</label>
                        <select name="type" class="form-control" id="type" required>
                            <option>Select type</option>
                            <option value="1">FTD</option>
                            <option value="2">RST</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="broker">Table Broker</label>
                        <select name="broker" class="form-control" id="broker" required>
                            <option>Select broker</option>
                            @foreach($brokers as $broker)
                                <option value="{{$broker->id}}">{{$broker->name}}</option>
                            @endforeach

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name">Table Name</label>
                        <input type="text" name="name" class="form-control" id="name" required>
                    </div>
                    <div class="form-group">
                        <label for="manager">Table Manager</label>
                        <input type="text" name="manager" class="form-control" id="manager" required>
                    </div>
                    <input type="submit" value="Create Table" class="btn btn-success">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>