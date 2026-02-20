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

        /* =========================================
           ОСНОВНОЙ LAYOUT
        ========================================= */

        html, body {
            height: 100%;
        }

        #layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================================
           SIDEBAR
        ========================================= */

        .sidebar {
            width: 260px;
            min-width: 260px;
            background: #343a40;
            color: #fff;
            transition: 0.3s ease;
        }

        .sidebar.collapsed {
            width: 0;
            min-width: 0;
            overflow: hidden;
        }

        .sidebar .nav-link {
            color: #adb5bd;
        }

        .sidebar .nav-link:hover {
            background: #495057;
            color: #fff;
        }

        /* =========================================
           CONTENT
        ========================================= */

        #content {
            flex: 1 1 auto;
            min-width: 0;
            background: #f8f9fa;
            transition: 0.3s ease;
        }

        .content-wrapper {
            width: 100%;
            padding: 32px;
            max-width: 100%;
        }

        /* =========================================
           БРЕЙКПОИНТЫ
        ========================================= */

        /* tablet */
        @media (min-width: 768px) {
            .content-wrapper {
                padding: 40px 48px;
            }
        }

        /* laptop */
        @media (min-width: 1024px) {
            .content-wrapper {
                padding: 48px 64px;
            }
        }

        /* desktop */
        @media (min-width: 1280px) {
            .content-wrapper {
                max-width: 1200px;
                margin: 0 auto;
            }
        }

        /* wide */
        @media (min-width: 1600px) {
            .content-wrapper {
                max-width: 1400px;
            }
        }

        /* ultra-wide (Full HD+) */
        @media (min-width: 1920px) {
            .content-wrapper {
                max-width: 1600px;
            }
        }

        /* 2K monitors */
        @media (min-width: 2560px) {
            .content-wrapper {
                max-width: 1900px;
            }
        }

        /* 4K monitors */
        @media (min-width: 3840px) {
            .content-wrapper {
                max-width: 2400px;
            }
        }

    </style>

    @stack('styles')
</head>
<body>

<div id="layout">

    <!-- SIDEBAR -->
    <div id="sidebar" class="sidebar d-flex flex-column">

        <div class="p-3 border-bottom border-secondary">
            <h5 class="mb-0">Отдел ККР</h5>
        </div>

        <ul class="nav flex-column p-2">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-house"></i> Главная
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-file-earmark"></i> Поручения
                </a>
            </li>
        </ul>

    </div>

    <!-- CONTENT -->
    <div id="content">

        <nav class="navbar navbar-light bg-white border-bottom px-3">
            <button class="btn btn-outline-secondary" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
        </nav>

        <div class="content-wrapper">

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

<script>
    document.getElementById('sidebarToggle').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('collapsed');
    });
</script>

@stack('scripts')

</body>
</html>
