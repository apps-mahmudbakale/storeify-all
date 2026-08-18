<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

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
    <link href="{{asset('css/auth.css')}}" rel="stylesheet">
    <link rel="shortcut icon" type="image/x-icon" href="{{!empty(app(App\Settings\StoreSettings::class)->favicon) ? asset('storage/settings/'.app(App\Settings\StoreSettings::class)->favicon):asset('favicon.png')}}">

    <!-- Scripts -->
    {{-- @vite(['resources/css/app.css', 'resources/js/app.js']) --}}
    
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
</head>
<body>
    @yield('content')
</body>
</html>
