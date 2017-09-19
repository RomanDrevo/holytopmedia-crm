<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Styles -->
    <link href="/css/app.css" rel="stylesheet">
    <link href="/css/main-app.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/css/login.css">
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
                'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
<div id="app">
    <div id="login-wrapper">
        @include('common.messages')
        @yield('content')
    </div>
</div>
<!-- Scripts -->
<script src="https://js.pusher.com/3.2/pusher.min.js"></script>
<script src="/js/app.js"></script>
<script src="/js/main-app.js"></script>

</body>
</html>
