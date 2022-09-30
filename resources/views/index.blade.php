<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
{{--        <meta name="viewport" content="width=device-width, initial-scale=1">--}}
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="icon" href="{{ asset('/images/icons/wave_favicon.png') }}"/>
        <title>Wave Fitness Project</title>
{{--        <img src="{{ asset('/images/barengage-logo.jpg') }}" alt="jacket3">--}}

        {{--        <meta name="csrf-token" content="{{ csrf_token() }}"/>--}}

{{--        <link href="{{ mix('css/bootstrap.css') }}" rel="stylesheet" type="text/css">--}}
{{--        <link href="{{ mix('css/adminApp.css') }}" rel="stylesheet" type="text/css">--}}
{{--        <link href="{{ mix('css/app.css') }}" rel="stylesheet" type="text/css">--}}
    </head>

    <body>
        <div id="app">
            <main-page></main-page>
        </div>

        <script src="{{ mix('js/app.js') }}"></script>
    </body>
</html>




