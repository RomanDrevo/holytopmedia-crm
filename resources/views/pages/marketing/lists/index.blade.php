@extends('layouts.app')

@section('content')
<email-lists :lists="{{ $lists }}"></email-lists>
@endsection
