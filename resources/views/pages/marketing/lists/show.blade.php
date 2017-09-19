@extends('layouts.app')

@section('content')
<list :listdata="{{ $list }}" :listid="{{ $list->id }}"></list>
@endsection
