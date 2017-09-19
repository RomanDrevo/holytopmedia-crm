@extends('layouts.app')

@section('content')
    <user-create :departments="{{$departments}}" :brokers="{{$brokers}}" :permissions="{{ $permissions }}"></user-create>
@endsection
