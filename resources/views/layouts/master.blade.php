<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf_token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SpreadSheet Demo') }}</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css"
          integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf"
          crossorigin="anonymous">
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
    @stack('styles')
</head>
<body>
<main id="app">
    @include('layouts.navigation')
    @yield('content')
</main>
<script src="{{ mix('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
