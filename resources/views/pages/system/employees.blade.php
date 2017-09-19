@extends('layouts.app')

@section('content')

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-usd"></i> All Employees
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="deposits_wrapper">
                    <div class="dataTable_wrapper">
                        <div class="col-md-12">
                            <div class="col-md-3 pull-left">
                                @if(\Request::has('query') || \Request::has('employee_id'))
                                    <a href="/system/employees" class="btn btn-warning btn-xs">Clean search results</a>
                                @endif
                            </div>
                            <div class="col-md-4 pull-right" style="margin-bottom: 18px;">
                                <form class="form-inline" method="GET" action="">
                                    <div class="form-group">
                                        <label for="query">Search:</label>
                                        <input type="text" class="form-control" name="query" id="query" value="{{ \Request::get('query') }}" placeholder="Search by name">
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-success" type="submit"><i class="fa fa-search"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover" id="dataTables-deposits">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Table</th>
                                    <th>Deposits Count</th>
                                    <th>Withdrawals Count</th>
                                    <th>Total Deposits</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->name }}</td>
                                        <td>{{ $employee->table ? $employee->table->name : "no table" }}</td>
                                        <td>{{ count($employee->deposits) }}</td>
                                        <td>{{ count($employee->withdrawals) }}</td>
                                        <td>{{ "$" . number_format($employee->deposits->sum(function($deposit){ return $deposit->amountUSD; }), 2) }}</td>
                                        <td>
                                            <form class="form-inline" method="GET" action="/system/csv-employee-deposits">
                                                <input type="hidden" name="csv_employee_id" value="{{ $employee->employee_crm_id }}">
                                                <button class="btn btn-success" type="submit" data-toggle="tooltip" data-placement="bottom" title="Download this month deposits to excel"><i class="fa fa-table"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row" style="text-align: center;">
                            {!! $employees->appends(\Request::except('page'))->render() !!}
                            <p class="show_pagination_count">{{ 'Showing Employees '.$employees->firstItem().' to '.$employees->lastItem().' out of '.$employees->total() }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
</div>
<!-- /.row -->
@endsection