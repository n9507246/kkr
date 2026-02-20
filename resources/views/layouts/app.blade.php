<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Отдел ККР
        @hasSection('title') - @yield('title') @endif
    </title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>

        /* ===================================================
           БАЗА
        =================================================== */

        html, body {
            height: 100%;
            margin: 0;
            overflow: hidden; /* убираем общий скролл */
        }

        #layout {
            display: flex;
            height: 100vh;
        }

        /* ===================================================
           SIDEBAR
        =================================================== */

        .sidebar {
            width: 16.25rem; /* 260px */
            min-width: 16.25rem;
            height: 100vh;
            background: #1e293b;
            color: #e2e8f0;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            border-right: 1px solid #334155;
        }

        .sidebar-header {
            padding: 1.5rem 1.5rem 1rem 1.5rem;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.03em;
            color: #fff;
        }

        .sidebar .nav {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .sidebar .nav-link {
            color: #94a3b8;
            border-radius: 0.5rem;
            padding: 0.6rem 0.9rem;
            margin-bottom: 0.3rem;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            margin-right: 0.6rem;
        }

        .sidebar .nav-link:hover {
            background: #334155;
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: #3b82f6;
            color: #fff;
        }

        /* Кастомный скроллбар sidebar */
        .sidebar::-webkit-scrollbar {
            width: 0.5rem;
        }

        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 1rem;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* ===================================================
           CONTENT
        =================================================== */

        #content {
            flex: 1;
            height: 100vh;
            overflow-y: auto;
            background: #f8fafc;
        }

        .content-wrapper {
            width: 100%;
            max-width: 100%;
            padding-left: 2rem;
            padding-right: 2rem;
        }

        /* Кастомный скроллбар контента */
        #content::-webkit-scrollbar {
            width: 0.6rem;
            padding: 0.2rem;
        }

        #content::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        #content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 1rem;
        }

        #content::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* ===================================================
           БРЕЙКПОИНТЫ (ТОЛЬКО ГОРИЗОНТАЛЬНЫЕ)
        =================================================== */

        @media (min-width: 48rem) { /* 768px */
            .content-wrapper {
                padding-left: 3rem;
                padding-right: 3rem;
            }
        }

        @media (min-width: 64rem) { /* 1024px */
            .content-wrapper {
                padding-left: 4rem;
                padding-right: 4rem;
            }
        }

        @media (min-width: 80rem) { /* 1280px */
            .content-wrapper {
                max-width: 75rem;
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (min-width: 100rem) { /* 1600px */
            .content-wrapper {
                max-width: 87.5rem;
            }
        }

        @media (min-width: 120rem) { /* 1920px */
            .content-wrapper {
                max-width: 100rem;
            }
        }

        @media (min-width: 160rem) { /* 2560px */
            .content-wrapper {
                max-width: 118.75rem;
            }
        }

        @media (min-width: 240rem) { /* 3840px */
            .content-wrapper {
                max-width: 150rem;
            }
        }

    </style>

    @stack('styles')
</head>
<body>

<div id="layout">

    <!-- SIDEBAR -->
    <x-sidebar :user="['name' => 'Иванов И.И.', 'role' => 'Администратор']" />

    <!-- CONTENT -->
    <div id="content">

        <div class="content-wrapper pt-4">

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

            @yield('content')

        </div>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<script src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>

@stack('scripts')

</body>
</html>
