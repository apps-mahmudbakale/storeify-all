<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" /> --}}

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', app(App\Settings\StoreSettings::class)->store_name) }}</title>

    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#3c8dbc">
    <meta name="description" content="A comprehensive inventory and sales management system for restaurants and medical facilities">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Storeify">
    <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    @livewireStyles
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('css/loader.css')}}">
    <link rel="shortcut icon" type="image/x-icon"
        href="{{ !empty(app(App\Settings\StoreSettings::class)->favicon) ? asset('storage/settings/' . app(App\Settings\StoreSettings::class)->favicon) : asset('favicon.png') }}">
    <style>
        .table tbody tr.success td {
            background-color: #dff0d8;
        }

        .table tbody tr.error td {
            background-color: #f2dede;
        }

        .table tbody tr.info td {
            background-color: #d9edf7;
        }

        .table tbody tr.warning td {
            background-color: lightgoldenrodyellow;
        }
    </style>
    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    <link rel="stylesheet" href="{{ asset('css/sweetalert2.min.css') }}">
    <script src="{{ asset('js/app.js') }}"></script>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        @include('partials.header')
        @include('partials.sidebar')
        @yield('content')
    </div>
    @livewireScripts
    <script src="{{ asset('js/sweetalert2.min.js') }}"></script>
    <script src="{{asset('js/localbase.dev.js')}}"></script>
    
    <!-- PWA Service Worker Registration -->
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('{{ asset("service-worker.js") }}', { scope: '/' })
                .then(registration => {
                    console.log('[PWA] Service Worker registered successfully:', registration);
                })
                .catch(error => {
                    console.warn('[PWA] Service Worker registration failed:', error);
                });
        }
    </script>
    
    <form id="logoutform" action="{{ route('app.logout') }}" method="POST" style="display: none;">
        {{ csrf_field() }}
    </form>
    <script>
        @if (session()->has('success'))
            Swal.fire({
                position: 'center',
                icon: 'success',
                title: '{{ session()->get('success') }}',
                showConfirmButton: true,
                timer: 2500
            })
        @elseif (session()->has('error'))
            Swal.fire({
                position: 'center',
                icon: 'error',
                title: '{{ session()->get('error') }}',
                showConfirmButton: true,
                timer: 5500
            })
        @endif
    </script>
</body>

</html>
