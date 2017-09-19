@extends('layouts.app')

@section('content')
    <sales-withdrawals :employees="{{ $employees }}" :tables="{{ $tables }}" :currencies="{{ $currencies  }}" :can_edit="{{ $hasPermissionsToEdit }}"></sales-withdrawals>
@endsection

@section('extra_scripts')

@endsection