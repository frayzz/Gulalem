<!-- resources/views/layouts/guest.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Layout</title>
    <!-- Вставьте сюда ваши CSS, JS и другие метаданные -->
    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
{{ $slot }}
</body>
</html>
