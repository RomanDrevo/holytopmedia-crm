@extends('layouts.scoreboard')


@section('content')

    <scoreboard-rst :videos_src="{{ $videos_src }}" :employees="{{$employees}}" :table="{{ $table }}" app_key="{{ env('PUSHER_APP_KEY') }}" broker="{{  \Auth::user()->broker->name }}"></scoreboard-rst>
@endsection
