@extends('layouts.app')

@section('content')
    <table-reports :table="{{ $table }}" :left_to_goal="{{ $left_to_goal }}"></table-reports>

@endsection

@section('extra_scripts')

@endsection
