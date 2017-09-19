@extends('layouts.app')

@section('content')
    <customers :countries="{{ $countries }}"></customers>


@endsection
