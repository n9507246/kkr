<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отдел ККР @hasSection('title') - @yield('title') @endif</title>

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    {{-- Дополнительные стили для сайдбара --}}


    {{-- Дополнительные стили из дочерних шаблонов --}}
    @stack('styles')
</head>
<body>
    <div class="wrapper">
        {{-- БОКОВАЯ ПАНЕЛЬ (теперь как компонент) 
        <x-sidebar :user="['name' => 'Иванов И.И.', 'role' => 'Администратор']" />--}}

        {{-- ОСНОВНОЙ КОНТЕНТ --}}
        <div id="content">
            <button type="button" id="sidebarToggle" class="toggle-btn">
                <i class="bi bi-list"></i> Меню
            </button>

            {{-- ХЛЕБНЫЕ КРОШКИ 
            @if(request()->route())
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}">Главная</a></li>
                    @if(request()->routeIs('orders.*'))
                        <li class="breadcrumb-item"><a href="{{ route('orders.index') }}">Поручения</a></li>
                        @if(request()->routeIs('orders.create'))
                            <li class="breadcrumb-item active">Создание</li>
                        @elseif(request()->routeIs('orders.edit'))
                            <li class="breadcrumb-item active">Редактирование</li>
                        @elseif(request()->routeIs('orders.show'))
                            <li class="breadcrumb-item active">Просмотр</li>
                        @elseif(request()->routeIs('orders.import.form'))
                            <li class="breadcrumb-item active">Импорт из Excel</li>
                        @endif
                    @endif
                </ol>
            </nav>
            @endif--}}

            {{-- СООБЩЕНИЯ --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="container">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>


    {{-- Стек для дополнительных скриптов --}}
    @stack('scripts')
</body>
</html>
