@extends('layouts.app')

@section('content')
    <edit-customers :user_id="{{ \Auth::user()->id }}" app_key="{{ env('PUSHER_APP_KEY') }}"></edit-customers>


@endsection

@section('extra_scripts')

@endsection