@extends('layouts.app')

@section("extra_styles")
<style>
    .datepicker{
        position: absolute !important;
    }
</style>
@endsection

@section('content')

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Settings</h1>
    </div>
    <!-- /.col-lg-12 -->
</div>

<!-- /.row -->
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <i class="fa fa-users"></i> Software Settings
            </div>
            <!-- /.panel-heading -->
            <div class="panel-body">
                <form method="POST" action="/system/settings/change-currency">
                    {{ csrf_field() }}
                    <h4>Currencies Exchange Rate</h4>
                    <hr>
                    <div class="form-group">
                        <label for="GBP/USD">GBP/USD:</label>
                        <input type="text" class="form-control" name="GBP/USD" id="GBP/USD" value="{{ $settings["GBP/USD"] }}">
                    </div>
                    <div class="form-group">
                        <label for="EUR/USD">EUR/USD:</label>
                        <input type="text" class="form-control" name="EUR/USD" id="EUR/USD" value="{{ $settings["EUR/USD"] }}">
                    </div>
                    <div class="form-group">
                        <label for="USD/NIS">USD/NIS:</label>
                        <input type="text" class="form-control" name="USD/NIS" id="USD/NIS" value="{{ $settings["USD/NIS"] }}">
                    </div>
                    <br/>
                    <div class="form-group">
                        <button class="btn btn-success pull-right" type="submit"><i class="fa fa-floppy-o"></i> Save Settings</button>
                    </div>
                </form>

                <form method="POST" action="/system/settings/update-currency-per-month">
                    {{ csrf_field() }}
                    <h4>Set rate per month</h4>
                    <hr>
                    <div class="form-group">
                        <label for="GBP_USD">GBP/USD:</label>
                        <input type="text" class="form-control" name="GBP_USD" id="GBP_USD" value="{{ $settings["GBP/USD"] }}">
                    </div>
                    <div class="form-group">
                        <label for="EUR_USD">EUR/USD:</label>
                        <input type="text" class="form-control" name="EUR_USD" id="EUR_USD" value="{{ $settings["EUR/USD"] }}">
                    </div>
                    <div class="form-group">
                        <label for="month_year">Month & Year:</label>
                        <input type="text" class="form-control month_year_picker" name="month_year" id="month_year">
                    </div>
                    <br/>
                    <div class="form-group">
                        <button class="btn btn-success pull-right" type="submit"><i class="fa fa-floppy-o"></i> Save Settings</button>
                    </div>
                </form>
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

        $(".month_year_picker").datepicker( {
            format: "mm-yyyy",
            viewMode: "months", 
            minViewMode: "months"
        });

        $('.manager_input').on('change', function(){
            var table_id = $(this).data('table-id');
            var manager = $(this).val();
            $.post('/system/update-table-manager', {
                table_id: table_id,
                manager : manager
            }, function(data){
                if(data == "error"){
                    alert("Something went wrong, please refresh the page.");
                }
            });
        });
    });
    </script>
@endsection