@extends('layouts.app')

@section("extra_styles")
<style>
.screen_btn {
    height: 51px;
    font-size: 22px;
    padding-top: 9px;
    border-radius: 1px;
    background: #524f4f;
    border-color: #484848;
}
.error-notice {
  margin: 5px 5px; /* Making sure to keep some distance from all side */
}
.header_panel {
    margin-top: 85px;
}

.oaerror {
  width: 100%; 
  margin: 0 auto; 
  background-color: #FFFFFF; 
  padding: 20px;
  border: 1px solid #eee;
  border-left-width: 5px;
  border-radius: 3px;
  margin: 0 auto;
  margin-bottom: 15px;
  font-family: 'Open Sans', sans-serif;
  font-size: 16px;
}

.danger {
  border-left-color: #d9534f; 
  background-color: rgba(217, 83, 79, 0.1); 
}

.danger strong {
  color:  #d9534f;
}

.warning {
  border-left-color: #f0ad4e;
  background-color: rgba(240, 173, 78, 0.1);
}

.warning strong {
  color: #f0ad4e;
}

.info {
  border-left-color: #5bc0de;
  background-color: rgba(91, 192, 222, 0.1);
}

.info strong {
  color: #5bc0de;
}

.success {
  border-left-color: #2b542c;
  background-color: rgba(43, 84, 44, 0.1);
}

.success strong {
  color: #2b542c;
}
.panel-success>.panel-heading {
    color: #3c763d;
    background-color: #eaeeea;
    border-color: #d6e9c6;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row header_panel">
        <h3>Welcome back, {{ $user->name }}</h3>
        <hr>
        <h4>Announcements:</h4>
        <div class="oaerror success">
            <strong>Scoreboard & Playground</strong> - With the new version of the system you can create a user that will be able to see only the screens of the company (Scoreboard and playground for sales floor). Simply create a new user with this permissions inside the Admin panel (For authorized users only).
            <br/><hr><br/>
            <strong>Customer support</strong> - comming soon...
            <br/><hr><br/>
            <strong>Risk Alert system</strong> - This feature is still is test mode an may show unaccurate results.
            <br/><hr><br/>
            <strong>Reports & Sales</strong> - You can now filter by almost any parameter and download a .CSV file of the results.
            <br/><hr><br/>
            <strong>Sales!!!</strong> - Make sure you assign all working employees to a table or else you will not see the deposit assigned to it.
        </div>
    </div>
    <hr style="margin-top: 100px;">
    <h4>Other:</h4>
    <div class="row">
        <div class="panel panel-success">
            <div class="panel-heading">Company Screens</div>

            <div class="panel-body">                
                <div class="col-md-6">
                    @can("jackpot")
                        <h3>General screens</h3>
                        <a href="/screens/jackpot" class="btn btn-success btn-block screen_btn">Open Jackpot</a>
                    @endcan
                </div>
                <div class="col-md-6">
                    @can("scoreboard")
                        <h3>Scoreboard screens</h3>
                        @foreach($tables as $table)
                            <a href="/screens/scoreboard/{{ $table->id }}" class="btn btn-success btn-block screen_btn">Scoreboard for {{ $table->name }}</a>
                        @endforeach
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
