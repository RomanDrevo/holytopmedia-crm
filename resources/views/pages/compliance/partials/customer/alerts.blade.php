<div class="panel panel-primary">
    <div class="panel-heading">Alerts</div>
    <div class="panel-body">
        @foreach($customer->alerts as $alert)
            <span class="label label-primary">{!! $alert->content !!}  </span>
        @endforeach
    </div>
</div>