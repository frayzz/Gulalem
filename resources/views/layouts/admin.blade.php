<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100">
<div class="container mx-auto p-4">
    <header class="mb-4">
        <h1 class="text-2xl font-bold">Admin Panel</h1>
        <nav>
            <ul class="flex space-x-4">
                <li><a href="{{ route('admin.products.index') }}" class="text-blue-500 hover:underline">Products</a></li>
                <!-- Добавьте другие ссылки для управления -->
            </ul>
        </nav>
    </header>
    <main>
        @yield('content')
    </main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
