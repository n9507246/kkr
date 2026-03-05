<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Отдел ККР
        @hasSection('title') - @yield('title') @endif
    </title>

    @vite(['resources/css/app.css'])
    {{ $styles ?? '' }}
    @stack('styles')
</head>
<body>

<div id="layout">
    <!-- SIDEBAR -->
    <x-sidebar :user="['name' => 'Иванов И.И.', 'role' => 'Администратор']" />

    <!-- CONTENT -->
    <div id="content">
        <div class="content-wrapper p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show">
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>
</div>

<!-- Vite JS -->
@vite(['resources/js/app.js'])

{{ $scripts ?? '' }}

@stack('scripts')

</body>
</html>
