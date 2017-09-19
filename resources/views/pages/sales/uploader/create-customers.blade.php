
@extends('layouts.app')

@section('content')
<create-customers :user_id="{{ \Auth::user()->id }}" app_key="{{ env('PUSHER_APP_KEY') }}"></create-customers>


@endsection

@section('extra_scripts')

@endsection