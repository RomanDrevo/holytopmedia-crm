@extends('layouts.login')

@section('content')
<div class="container">
    <div class="card card-container">
        <form class="form-signin" role="form" method="POST" action="{{ url('/login') }}">
            {{ csrf_field() }}

            <span id="reauth-email" class="reauth-email"></span>
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" placeholder="Email address" required autofocus>
                @if ($errors->has('email'))
                    <span class="help-block">
                        <strong>{{ $errors->first('email') }}</strong>
                    </span>
                @endif
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                @if ($errors->has('password'))
                    <span class="help-block">
                        <strong>{{ $errors->first('password') }}</strong>
                    </span>
                @endif
            </div>
            <div id="remember" class="checkbox">
                <label>
                    <input type="checkbox" name="remember"> Remember me
                </label>
            </div>
            <button class="btn btn-lg btn-primary btn-block btn-signin" type="submit">Sign in</button>
        </form><!-- /form -->
        <a href="{{ url('/password/reset') }}" class="forgot-password">
            Forgot the password?
        </a>
    </div><!-- /card-container -->
</div><!-- /container -->
@endsection
