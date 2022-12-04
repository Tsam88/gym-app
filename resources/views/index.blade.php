<!DOCTYPE html>
<html lang="en">

    <head>
{{--        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">--}}
{{--        <meta name="viewport" content="width=device-width, initial-scale=1">--}}
{{--        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">--}}
{{--        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=12, user-scalable=no">--}}
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">

        <!-- Google Font -->
        <link href="https://fonts.googleapis.com/css?family=Muli:300,400,500,600,700,800,900&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,500,600,700&display=swap" rel="stylesheet">
{{--        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Poppins:300,400,500,600,700,800,900&display=swap" />--}}
{{--        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=arquitecta:300,400,500,600,700,800,900&display=swap" />--}}
{{--        <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Proxima+Nova:300,400,500,600,700,800,900&display=swap" />--}}


        <link rel="icon" href="{{ asset('/images/wave_transparent_no_buffer.png') }}"/>
        <title>Wave Fitness Project</title>

    </head>

    <body>
        <div id="app">
            <main-page></main-page>
        </div>

        <script src="https://unpkg.com/vue-select@latest"></script>
        <script src="{{ mix('js/app.js') }}"></script>
{{--        <script src="{{ mix('js/jquery-3.3.1.min.js') }}"></script>--}}
{{--        <script src="{{ mix('js/bootstrap.min.js') }}"></script>--}}
        <script src="{{ mix('js/jquery.magnific-popup.min.js') }}"></script>
        <script src="{{ mix('js/masonry.pkgd.min.js') }}"></script>
        <script src="{{ mix('js/jquery.barfiller.js') }}"></script>
        <script src="{{ mix('js/jquery.slicknav.js') }}"></script>
        <script src="{{ mix('js/owl.carousel.min.js') }}"></script>
        <script src="{{ mix('js/main.js') }}"></script>
    </body>
</html>




