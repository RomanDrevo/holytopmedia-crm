@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="x_panel">
                <div class="x_title">
                    <h2>All Users</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-3 pull-left">
                            @if(\Request::has('query') || \Request::has('employee_id'))
                                <a href="/admin/users" class="btn btn-warning btn-xs">Clean search results</a>
                            @endif
                        </div>
                        <div class="col-md-9 pull-right" style="margin-bottom: 18px;">
                            @include('pages.admin.partials.search-form')
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">User ID</th>
                                <th class="column-title">Department Name</th>
                                <th class="column-title">Name </th>
                                <th class="column-title">Email </th>
                                <th class="column-title">Actions </th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                                <tr class="even pointer">
                                    <td>{{ $user->id }}</td>
                                    <td>{{ ucfirst($user->department->name) }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>

                                    <td>
                                        <a href="/admin/users/{{ $user->id }}" type="button" class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="bottom" title="Edit">
                                            <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                        </a>

                                        <form style="display:inline-block" class="action-buttons" method="POST" action="{{url('/admin/users/' . $user->id . '/delete/' )}}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="_method" value="DELETE" />
                                            <button type="submit" class="btn btn-danger btn-xs" data-toggle="tooltip" data-placement="bottom" title="Delete"><i class="fa fa-trash-o"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                        <div class="row" class="text-center" style="margin: 0px auto;">
                            {!! $users->appends(\Request::except('page'))->render() !!}
                            <p class="show_pagination_count">{{ 'Showing customers '.$users->firstItem().' to '.$users->lastItem().' out of '.$users->total() }}</p>
                        </div>

                        <div class="action_buttons pull-left">
                            <a href="users/create" class="btn btn-success">Create new User</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection