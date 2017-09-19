<form class="form-inline" method="GET" action="" style="float: right;">
    <div class="form-group">
        {{ csrf_field() }}
        <label for="query">Search:</label>
        <input type="text" class="form-control" name="query" id="query" value="{{ \Request::get('query') }}">
    </div>
    <div class="form-group">  
        <?php $field = \Request::has('field') ? \Request::get('field') : "id"; ?>      
        <select class="form-control" name="field">
            <option value="id" @if( $field == 'id') 'selected' @endif>By ID</option>
            <option value="customerId" @if( $field == 'customerId') 'selected' @endif>By Customer ID</option>
            <option value="transactionId" @if( $field == 'transactionId') 'selected' @endif>By Transaction ID</option>
        </select>
    </div>
    <div class="form-group">
        @if( \Request::has('employee_id'))
            <select class="form-control nice-select" name="employee_id">
                <option>No worker selected</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->employee_crm_id }}" @if( \Request::get('employee_id') == $employee->employee_crm_id ) selected @endif>{{ $employee->name }}</option>
                @endforeach
            </select>
        @else
            <select class="form-control nice-select" name="employee_id">
                <option>No worker selected</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->employee_crm_id }}">{{ $employee->name }}</option>
                @endforeach
            </select>
        @endif
    </div>
    <div class="form-group">
        <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
    </div>
</form>