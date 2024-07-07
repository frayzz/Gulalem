<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Включение метатегов -->
    @include('partials.meta')

    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
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
