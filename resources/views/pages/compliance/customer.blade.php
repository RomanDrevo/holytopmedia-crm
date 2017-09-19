@extends('layouts.app')

@section('extra_styles')
<link href="/bower_components/fancybox/source/jquery.fancybox.css" rel="stylesheet">
<link href="/css/datatables.min.css" rel="stylesheet">
@endsection

@section('content')
    <customer-info :customer="{{  $customer }}"></customer-info>
@include('pages.compliance.partials.customer.modals.send-dod')
@include('pages.compliance.partials.customer.modals.file-comments')
@endsection

@section('extra_scripts')
<script src="/bower_components/fancybox/source/jquery.fancybox.js"></script>
<script src="/js/customer.js?time="{{time()}}></script>
@endsection
