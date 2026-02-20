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
    overflow: hidden;
}

#layout {
    display: flex;
    height: 100vh;
}

/* ===================================================
   SIDEBAR (Более яркий и аккуратный)
=================================================== */

.sidebar {
    width: 16.25rem;
    min-width: 16.25rem;
    height: 100vh;

    /* Чуть светлее и глубже */
    background: linear-gradient(
        180deg,
        #273449 0%,
        #1f2a3c 100%
    );

    color: #e2e8f0;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    overflow-x: hidden;

    border-right: 1px solid #3b4a63;
    box-shadow: 0 0 1.2rem rgba(0, 0, 0, 0.25);
}

/* Заголовок */
.sidebar-header {
    padding: 1.75rem 1.5rem 1.25rem 1.5rem;
    font-weight: 600;
    font-size: 1rem;
    letter-spacing: 0.04em;
    color: #ffffff;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}

/* Навигация */
.sidebar .nav {
    padding-left: 1rem;
    padding-right: 1rem;
    margin-top: 1rem;
}

.sidebar .nav-link {
    color: #a8b4c8;
    border-radius: 0.6rem;
    padding: 0.7rem 1rem;
    margin-bottom: 0.4rem;
    font-size: 0.92rem;
    transition: all 0.2s ease;
}

.sidebar .nav-link i {
    margin-right: 0.75rem;
    font-size: 0.95rem;
}

/* Hover */
.sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.08);
    color: #ffffff;
    transform: translateX(0.15rem);
}

/* Active */
.sidebar .nav-link.active {
    background: #3b82f6;
    color: #ffffff;
    box-shadow: 0 0.4rem 0.8rem rgba(59,130,246,0.35);
}

/* Скроллбар sidebar */
.sidebar::-webkit-scrollbar {
    width: 0.5rem;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #51607a;
    border-radius: 1rem;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: #6b7c98;
}

/* ===================================================
   CONTENT
=================================================== */

#content {
    flex: 1;
    height: 100vh;
    overflow-y: auto;
    background: #f1f5f9;
}

.content-wrapper {
    width: 100%;
    max-width: 100%;
    padding-left: 2rem;
    padding-right: 2rem;
    padding-top: 1.5rem; /* добавили немного вертикального воздуха */
}

/* Скроллбар контента */
#content::-webkit-scrollbar {
    width: 0.6rem;
}

#content::-webkit-scrollbar-track {
    background: #e2e8f0;
}

#content::-webkit-scrollbar-thumb {
    background: #b8c4d6;
    border-radius: 1rem;
}

#content::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* ===================================================
   БРЕЙКПОИНТЫ (ТОЛЬКО ГОРИЗОНТАЛЬНЫЕ)
=================================================== */

@media (min-width: 48rem) {
    .content-wrapper {
        padding-left: 3rem;
        padding-right: 3rem;
    }
}

@media (min-width: 64rem) {
    .content-wrapper {
        padding-left: 4rem;
        padding-right: 4rem;
    }
}

@media (min-width: 80rem) {
    .content-wrapper {
        max-width: 75rem;
        margin-left: auto;
        margin-right: auto;
    }
}

@media (min-width: 100rem) {
    .content-wrapper {
        max-width: 87.5rem;
    }
}

@media (min-width: 120rem) {
    .content-wrapper {
        max-width: 100rem;
    }
}

@media (min-width: 160rem) {
    .content-wrapper {
        max-width: 118.75rem;
    }
}

@media (min-width: 240rem) {
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
