@extends('layouts.app')

@section('content')
    <reports-deposits :employees="{{ $employees }}" :tables="{{ $tables }}" :currencies="{{ $currencies  }}" ></reports-deposits>

@endsection

@section('extra_scripts')

@endsection