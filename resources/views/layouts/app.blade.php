<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- ============================================
         STYLE GLOBAL - CSS Plugins
         ============================================ -->
    <!-- plugins:css -->
    @include('layouts.styleglobal')
    <!-- endinject -->

    <!-- ============================================
         STYLE PAGE - Page Specific CSS
         ============================================ -->
    <!-- inject:css -->
     @include('layouts.stylepage')
    <!-- endinject -->

    <!-- ============================================
         HEADER SECTION
         ============================================ -->
    @yield('header')
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
</head>
<body>
    <div class="container-scroller">
        
        <!-- ============================================
             NAVBAR SECTION
             ============================================ -->
        @include('layouts.navbar')

        <div class="container-fluid page-body-wrapper">
            
            <!-- ============================================
                 SIDEBAR SECTION
                 ============================================ -->
            @include('layouts.sidebar')

            <div class="main-panel">
                
                <!-- ============================================
                     CONTENT SECTION
                     ============================================ -->
                <div class="content-wrapper">
                    @yield('content')
                </div>
                <!-- content-wrapper ends -->

                <!-- ============================================
                     FOOTER SECTION
                     ============================================ -->
                @include('layouts.footer')
                
            </div>
            <!-- main-panel ends -->
        </div>
        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- ============================================
         JAVASCRIPT GLOBAL - JS Plugins
         ============================================ -->
    <!-- plugins:js -->
     @include('layouts.jvglobal')
    <!-- endinject -->

    <!-- ============================================
         JAVASCRIPT PAGE - Page Specific JS
         ============================================ -->
    <!-- inject:js -->
     @include('layouts.javascriptpage')
    <!-- endinject -->
<!-- </body>
</html> -->
