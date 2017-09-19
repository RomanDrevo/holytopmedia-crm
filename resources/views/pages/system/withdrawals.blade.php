@extends('layouts.app')

@section('content')
    <system-withdrawals :employees="{{ $employees }}" :tables="{{ $tables }}" :currencies="{{ $currencies  }}"></system-withdrawals>
@endsection

@section('extra_scripts')

@endsection