<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Cache-Control" content="no-store" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="0" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- plugins:css -->
    @include('layouts.styleglobal')
    <!-- endinject -->
    >
    <!-- inject:css -->
    @include('layouts.stylepage')
    <!-- endinject -->

    @yield('header')
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>

<body>
    <div class="container-scroller">


        @include('layouts.navbar')

        <div class="container-fluid page-body-wrapper">

            @include('layouts.sidebar')

            <div class="main-panel">

                <div class="content-wrapper">
                    @yield('content')
                </div>
                <!-- content-wrapper ends -->

                @include('layouts.footer')

            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    @include('layouts.jvglobal')
    <!-- endinject -->

    <!-- inject:js -->
    @include('layouts.javascriptpage')
    <!-- endinject -->
    <!-- </body>
</html> -->