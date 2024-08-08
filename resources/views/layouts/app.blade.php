<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Включение метатегов -->
    @include('partials.meta')

    <!-- Канонический тег -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="icon" href="{{ secure_asset('images/favicon.ico') }}" type="image/x-icon">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('images/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('images/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('images/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ secure_asset('images/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ secure_asset('images/safari-pinned-tab.svg') }}" color="#007ba1">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- Подключение CSS -->
    @vite('resources/css/app.css')
</head>
<body>
@include('partials.header')
<div class="container">
    @yield('content')
</div>
@if(Route::is('public.index'))
    @include('partials.footer')
@endif

@vite('resources/js/app.js')
</body>
