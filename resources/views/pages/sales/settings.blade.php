@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Settings</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <i class="fa fa-users"></i> Sales Settings
                </div>
                <div class="panel-body">

                    <form method="POST" action="/sales/settings/set-month-goal">
                        {{ csrf_field() }}
                        <h4>Company Monthly Goal</h4>
                        <hr>
                        <div class="form-group">
                            <label for="monthly_goal">Goal:</label>
                            <input type="text" class="form-control" name="monthly_goal" id="monthly_goal" value="{{ $settings["monthly_goal"] }}">
                        </div>

                        <div class="form-group">
                            <button class="btn btn-success pull-right" type="submit"><i class="fa fa-floppy-o"></i> Save Settings</button>
                        </div>
                    </form>

                    <br>
                    <br>
                    <br>

                    <form method="POST" action="/sales/settings/set-auto-approved-processor">
                        {{ csrf_field() }}
                        <h4>Set Auto Approved Processors</h4>
                        <hr>

                        <div class="form-group">
                            <label for="auto_approved_processor">Auto Approved Processor:</label>
                            <input type="text" class="form-control" name="auto_approved_processor" id="auto_approved_processor" value="{{ $settings["auto_approved_processors"] }}">
                            <p style="padding:10px 0;"><b>Separate words with | sign.(Example: Fibonatix1 | Inatec )</b></p>
                        </div>

                        <div class="form-group">
                            <button class="btn btn-success pull-right" type="submit"><i class="fa fa-floppy-o"></i> Save Settings</button>
                        </div>
                    </form>

                    <br><hr><br>
                    <winner-video></winner-video>
                </div>
            </div>
        </div>
    </div>
@endsection

