@if(Session::has("error"))
<div class="alert alert-danger">
    <a href="#"class="close" data-dismiss="alert">&times;</a>
    <span>{!!Session::get("error")!!}</span>
</div>
@endif
@if(count($errors->all()))
<div class="alert alert-danger">
    <a href="#"class="close" data-dismiss="alert">&times;</a>
    <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</div>
@endif
@if(Session::has("success"))
<div class="alert alert-success">
    <a href="#"class="close" data-dismiss="alert">&times;</a>
    <span>{!!Session::get("success")!!}</span>
</div>
@endif
@if(Session::has("message"))
<div class="alert alert-info">
    <a href="#"class="close" data-dismiss="alert">&times;</a>
    <span>{!!Session::get("message")!!}</span>
</div>
@endif