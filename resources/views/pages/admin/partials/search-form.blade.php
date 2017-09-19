<form class="form-inline" method="GET" action="" style="float: right">
    <div class="form-group">
        {{ csrf_field() }}
        <label for="query">Search:</label>
        <input type="text" class="form-control" name="query" id="query" value="{{ \Request::get('query') }}">
    </div>
    <div class="form-group">
        <?php $field = \Request::has('field') ? \Request::get('field') : "id"; ?>
        <select class="form-control" name="field">
            <option value="id">By ID</option>
            <option value="name" @if( $field == 'Name') selected @endif>By Name</option>
            <option value="email" @if( $field == 'email') selected @endif>By Email</option>
        </select>
    </div>
    <div class="form-group">
        <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
    </div>
</form>