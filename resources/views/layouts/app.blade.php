<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Gulalem - твой цветочный мир')</title>
    <meta name="description" content="Купить цветы и букеты в GULALEM. Популярные, классические, моно и смешанные букеты. Закажите сейчас!">
    <meta name="keywords" content="цветы, букеты, купить цветы, GULALEM, классические букеты, моно-букеты, смешанные букеты">
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
