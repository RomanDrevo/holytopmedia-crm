@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Reports</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-users"></i> All Employees Reports
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <div id="deposits_wrapper">
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="dataTables-employees">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Deposits</th>
                                    <th>Total Income</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($employees as $employee)
                                    <tr>
                                        <td>{{ $employee->id }}</td>
                                        <td>{{ $employee->name }}</td>
                                        <td style="max-width: 30%;">
                                            <ul>
                                                @if( count($employee->deposits) == 0 )
                                                    No Deposits
                                                @else
                                                    @foreach($employee->deposits as $deposit)
                                                        <li>Created: {{ $deposit->confirm_time->format("m-d-Y H:i") }}, Amount: ${{ number_format($deposit->amount) }}</li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </td>
                                        <td>
                                            <?php
                                                $total = $employee->deposits->sum(function ($deposit) {
                                                    return $deposit->amount;
                                                });
                                                echo "$" . number_format($total);
                                            ?>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="row" style="text-align: center;">
                            {!! $employees->appends(\Request::except('page'))->render() !!}
                            <p class="show_pagination_count">{{ 'Showing employees '.$employees->firstItem().' to '.$employees->lastItem().' out of '.$employees->total() }}</p>
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

@section('extra_scripts')
<script>
    $(document).ready(function() {

        $('.nice-select').select2();

        $('body').on('change', '.table-selector', function(){
            var employee_id = $(this).data('employee-id');
            var table = $(this).val();
            $.post('/admin/update-user-table', {
                table: table,
                employee_id : employee_id
            }, function(data){
                if(data == "error"){
                    alert("Something went wrong, please refresh the page.");
                }
            });
        });
    });
    </script>
@endsection