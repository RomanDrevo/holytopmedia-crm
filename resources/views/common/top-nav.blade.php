<div class="navbar-header">
    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
    </button>
    <a class="navbar-brand" href="/">
        @if (\Auth::user()->broker->id== 1)
        <img src="/images/ivory_logo.png" style="height: 70px;">

        @elseif (\Auth::user()->broker->id== 2)
        <img class="logo72" src="/images/72option_logo.png" style="max-height: 50px; max-width: 205px; margin-top: 10px;margin-left: 10px;">
        @endif
    </a>
</div>

<ul class="nav navbar-top-links navbar-right">

    <li>
        <form method="POST" action="/logout" style="margin-top: 22px;">
            {{ csrf_field() }}
            <button type="submit"><i class="fa fa-sign-out fa-fw"></i> Logout</button>
        </form>
    </li>
</ul>