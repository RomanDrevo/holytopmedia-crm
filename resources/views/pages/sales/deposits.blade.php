@extends('layouts.app')

@section('content')

    <sales-deposits
            :employees="{{ $employees }}"
            :tables="{{ $tables }}"
            :currencies="{{ $currencies  }}"
            :can_edit="{{ $hasPermissionsToEdit }}">
    </sales-deposits>


@endsection

@section('extra_scripts')

@endsection