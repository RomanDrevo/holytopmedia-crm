@extends('layouts.app')

@section('content')
    <style>
        .type1{
            color: #3c763d;
            background-color: #dff0d8;
            border-color: #d6e9c6;
        }

        .type2{
            color: #31708f;
            background-color: #d9edf7;
            border-color: #bce8f1;
        }

        .type3{
            color: #a94442;
            background-color: #f2dede;
            border-color: #ebccd1;
        }
    </style>

    <div class="container">
        <div class="row">
            <div class="x_panel">
                <div class="x_title">
                    <h2>Reports</h2>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <div class="row">
                        <div class="col-md-4 pull-right" style="margin-bottom: 18px;">
                            @include('pages.compliance.partials.search-form')
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped jambo_table bulk_action">
                            <thead>
                            <tr class="headings">
                                <th class="column-title">Employee</th>
                                <th class="column-title">Comments</th>
                                <th class="column-title">Owned</th>
                                <th class="column-title">Documents</th>
                                <th class="column-title">Total</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->comments->count() }}</td>
                                    <td>{{ $user->monthlyReports ? $user->monthlyReports->first_comment_count : 0 }}</td>
                                    <td>{{ $user->monthlyReports ? $user->monthlyReports->documents_count : 0 }}</td>
                                    <td></td>
                                </tr>
                             @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
