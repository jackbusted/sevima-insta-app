<!doctype html>
<html lang="en">
    <head>
        <title>ITATS's TEFL - {{ $title }}</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" href="{{ asset('css/login/style.css') }}">
    </head>

    <body class="img js-fullheight" style="background-image: url({{ asset('images/bg.jpg') }});">
        <section class="ftco-section">
            <div class="container pb-5">
                <div class="row justify-content-center">
                    <div class="text-center mb-5">
                        <h2 class="heading-section mt-5"><img src="{{ asset('images/logo-mid.png') }}" style="max-height: 80px;"></h2>
                    </div>
                </div>
                @yield('logreg_content')
            </div>
        </section>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
        <script src="{{ asset('js/login/jquery.min.js') }}"></script>
        <script src="{{ asset('js/login/popper.js') }}"></script>
        <script src="{{ asset('js/login/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/login/main.js') }}"></script>
    </body>
</html>