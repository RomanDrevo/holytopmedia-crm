@extends('layouts.app')
@section('content')
    <sales-reports :left_to_goal="{{$left_to_goal}}" :customers="{{ $customerByHours }}"
                   :countries="{{ $countries }}" :campaigns="{{ $campaigns }}"></sales-reports>
@endsection
@section('extra_scripts')
@endsection

