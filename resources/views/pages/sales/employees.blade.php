


@extends('layouts.app')



@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Employees</h1>
        </div>
    </div>

    <employees-view :tables="{{ $tables }}"></employees-view>

@endsection


@section('extra_scripts')
@endsection