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
    <link href="/css/main-app.css" rel="stylesheet">
    <link href="{{ mix('/css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/flatpickr/dist/flatpickr.min.css">
    @yield("extra_styles")
    <!-- Scripts -->
    <script>
        window.Laravel = <?php echo json_encode([
            'csrfToken' => csrf_token(),
        ]); ?>
    </script>
</head>
<body>
    <div id="app">
        @include('common.navigation')
        <div id="page-wrapper">
            @include('common.messages')
            @yield('content')
        </div>
    </div>
    <!-- Scripts -->
    <script src="https://js.pusher.com/3.2/pusher.min.js"></script>
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.17.1/moment.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.45/js/bootstrap-datetimepicker.min.js"></script>
    <script src="/js/main-app.js"></script>
    <script src="{{ mix('/js/app.js') }}"></script>
    @yield('extra_scripts')
    <script>
        $(function(){
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
</body>
</html>
