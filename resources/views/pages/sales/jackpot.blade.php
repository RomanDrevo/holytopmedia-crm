@extends('layouts.jackpot_wrapper')

@section('content')
    <div class="container-fluid">


        <playground broker="{{  \Auth::user()->broker->name }}" :last-winners="{{ $lastWinners }}" :videos_src="{{ $videos_src }}" app-key="{{ env('PUSHER_APP_KEY') }}"></playground>

    </div>
@endsection