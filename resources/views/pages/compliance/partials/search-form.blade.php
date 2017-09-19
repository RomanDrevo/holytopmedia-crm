<form class="form-inline" method="GET" action="">
    <div class="form-group">
        {{ csrf_field() }}
        <label for="query">Search:</label>
        <input type="text" class="form-control" name="query" id="query" value="{{ \Request::get('query') }}">
    </div>
    <div class="form-group">  
        <?php $field = \Request::has('field') ? \Request::get('field') : "id"; ?>      
        <select class="form-control" name="field">
            <option value="customer_crm_id">By ID</option>
            <option value="FirstName" @if( $field == 'FirstName') 'selected' @endif>By First Name</option>
            <option value="LastName" @if( $field == 'LastName') 'selected' @endif>By Last Name</option>
            <option value="email" @if( $field == 'email') 'selected' @endif>By Email</option>
            <option value="Phone" @if( $field == 'Phone') 'selected' @endif>By Phone</option>
        </select>
    </div>
    <div class="form-group">
        <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
    </div>
</form>