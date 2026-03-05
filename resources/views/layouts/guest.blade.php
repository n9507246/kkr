<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>

    @vite(['resources/css/app.css', 'resources/js/guest.js'])

    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .min-vh-100 {
            min-height: 100vh;
        }
    </style>
</head>
<body>
    @yield('content')

    @stack('scripts')
</body>
</html>
