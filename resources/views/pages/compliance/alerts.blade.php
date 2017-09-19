@extends('layouts.app')


@section('content')

    <alert-lists></alert-lists>

@endsection


@section('extra_scripts')
    <script type="text/javascript">
        $(document).ready(function () {

            var hiddenInput = $("#alert_type");

            $(".alert-filter").on("click", function (e) {
                e.preventDefault();
                var typeID = $(this).data("type");
                console.log(typeID);
                hiddenInput.val(typeID);

                $("#filter-form").submit();

            });

        });
    </script>
@endsection