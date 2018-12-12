<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>

<div id="app">

    <div class="container">

        <div class="row col-md-12 d-flex justify-content-md-center m-0 mt-5">

            @yield('content')

        </div>

    </div>

</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

</body>
</html>


