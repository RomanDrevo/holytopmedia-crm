@extends('layouts.app')

@section('extra_styles')
    <link href="/public/bower_components/fancybox/source/jquery.fancybox.css" rel="stylesheet">
@endsection
@section('content')
    <user-edit :user="{{$u}}" :permissions="{{$u->permissions}}" :departments="{{$departments}}"
               :brokers="{{$brokers}}"></user-edit>
@endsection

