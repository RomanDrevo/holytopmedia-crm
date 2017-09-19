@extends('layouts.app')

@section('content')
<div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                @if(count($errors))
                    <div class="alert alert-danger">
                        @foreach ($errors->all() as $error)
                            {{ $error }} <br/>
                        @endforeach
                    </div>
                @endif
                <div class="panel panel-default">
                    <div class="panel-heading">Create a new list</div>

                    <div class="panel-body">
                        <form action="/marketing/lists/create" method="POST">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label for="name">Name:</label>
                                <input type="text" name="name" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="target_url">Target URL:</label>
                                <input type="text" name="target_url" class="form-control">
                            </div>  
                            <div class="form-group">
                                <label for="broker">Broker:</label>
                                <select name="broker" class="form-control">
                                    @foreach($brokers as $broker)
                                        <option value="{{ $broker->id }}">{{ $broker->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="pull-right">
                                <input type="submit" value="Save" class="btn btn-primary" />
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
