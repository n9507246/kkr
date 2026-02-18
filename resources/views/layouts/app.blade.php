<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отдел ККР</title>
    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- Дополнительные стили для сайдбара --}}
    <style>
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        
        #sidebar {
            min-width: 280px;
            max-width: 280px;
            min-height: 100vh;
            background: linear-gradient(180deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            transition: all 0.3s;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        #sidebar.active {
            margin-left: -280px;
        }
        
        #sidebar .sidebar-header {
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        #sidebar .sidebar-header h3 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 300;
        }
        
        #sidebar ul.components {
            padding: 20px 0;
        }
        
        #sidebar ul li a {
            padding: 12px 20px;
            font-size: 1rem;
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        #sidebar ul li a i {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
        }
        
        #sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border-left-color: #fff;
        }
        
        #sidebar ul li.active > a {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border-left-color: #fff;
            font-weight: 500;
        }
        
        #sidebar ul li a .badge {
            margin-left: auto;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }
        
        /* Стили для подменю */
        #sidebar ul li ul {
            background: rgba(0, 0, 0, 0.1);
        }
        
        #sidebar ul li ul li a {
            padding-left: 56px;
            font-size: 0.95rem;
        }
        
        #sidebar ul li ul li.active a {
            background: rgba(255, 255, 255, 0.15);
            border-left-color: #fff;
        }
        
        #content {
            width: 100%;
            padding: 20px;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        
        .toggle-btn {
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 12px;
            margin-bottom: 20px;
            display: none;
        }
        
        @media (max-width: 768px) {
            #sidebar {
                margin-left: -280px;
            }
            #sidebar.active {
                margin-left: 0;
            }
            .toggle-btn {
                display: inline-block;
            }
        }
        
        .nav-divider {
            height: 1px;
            background: rgba(255, 255, 255, 0.2);
            margin: 15px 20px;
        }
        
        .dropdown-toggle::after {
            margin-left: auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        {{-- БОКОВАЯ ПАНЕЛЬ --}}
        <nav id="sidebar">
            <div class="sidebar-header">
                <h3><i class="bi bi-map"></i> Отдел ККР</h3>
                <p class="small text-white-50 mb-0">Комплексные кадастровые работы</p>
            </div>

            <ul class="list-unstyled components">
                
                {{-- ВСЕ РАБОТЫ --}}
                <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                    <a href="{{ route('home') }}">
                        <i class="bi bi-grid"></i>
                        Все работы
                    </a>
                </li>
                
                {{-- ПОРУЧЕНИЯ - ОСНОВНОЕ МЕНЮ С ПОДМЕНЮ --}}
                <li class="{{ request()->routeIs('orders.*') ? 'active' : '' }}">
                    <a href="#ordersSubmenu" data-bs-toggle="collapse" aria-expanded="{{ request()->routeIs('orders.*') ? 'true' : 'false' }}" class="dropdown-toggle">
                        <i class="bi bi-file-text"></i>
                        Поручения УРР
                        <span class="badge rounded-pill">5</span>
                    </a>
                    <ul class="collapse {{ request()->routeIs('orders.*') ? 'show' : '' }} list-unstyled" id="ordersSubmenu">
                        {{-- Все поручения --}}
                        <li class="{{ request()->routeIs('orders.index') ? 'active' : '' }}">
                            <a href="{{ route('orders.index') }}">
                                <i class="bi bi-list-ul"></i>
                                Все поручения
                            </a>
                        </li>
                        
                        {{-- Создать поручение --}}
                        <li class="{{ request()->routeIs('orders.create') ? 'active' : '' }}">
                            <a href="{{ route('orders.create') }}">
                                <i class="bi bi-plus-circle"></i>
                                Создать поручение
                            </a>
                        </li>
                        
                        {{-- Импорт из Excel --}}
                        <li class="{{ request()->routeIs('orders.import.form') ? 'active' : '' }}">
                            <a href="{{ route('orders.import.form') }}">
                                <i class="bi bi-file-excel"></i>
                                Импорт из Excel
                            </a>
                        </li>
                        
                        {{-- Экспорт (на будущее) --}}
                        <li>
                            <a href="{{ route('orders.export') }}">
                                <i class="bi bi-download"></i>
                                Экспорт
                            </a>
                        </li>
                    </ul>
                </li>
                
                {{-- РЕДАКТИРОВАНИЕ (показывается только когда мы редактируем) --}}
                @if(request()->routeIs('orders.edit'))
                <li class="ms-4 active">
                    <a href="#">
                        <i class="bi bi-pencil"></i>
                        Редактирование: {{ request()->route('order') ?? '' }}
                    </a>
                </li>
                @endif
                
                {{-- ПРОСМОТР (показывается только когда смотрим) --}}
                @if(request()->routeIs('orders.show'))
                <li class="ms-4 active">
                    <a href="#">
                        <i class="bi bi-eye"></i>
                        Просмотр поручения
                    </a>
                </li>
                @endif
                
                <div class="nav-divider"></div>
                
                {{-- ДОПОЛНИТЕЛЬНЫЕ РАЗДЕЛЫ (можно добавить позже) --}}
                <li>
                    <a href="#reportsSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">
                        <i class="bi bi-bar-chart"></i>
                        Отчеты
                    </a>
                    <ul class="collapse list-unstyled" id="reportsSubmenu">
                        <li>
                            <a href="/reports/monthly">
                                <i class="bi bi-calendar"></i>
                                Месячный отчет
                            </a>
                        </li>
                        <li>
                            <a href="/reports/quarterly">
                                <i class="bi bi-calendar3"></i>
                                Квартальный отчет
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
            
            {{-- ИНФОРМАЦИЯ О ПОЛЬЗОВАТЕЛЕ --}}
            <div class="sidebar-footer p-3 border-top border-white-50">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <div>
                        <small class="d-block text-white-50">Администратор</small>
                        <strong>Иванов И.И.</strong>
                    </div>
                </div>
            </div>
        </nav>

        {{-- ОСНОВНОЙ КОНТЕНТ --}}
        <div id="content">
            <button type="button" id="sidebarToggle" class="toggle-btn">
                <i class="bi bi-list"></i> Меню
            </button>
            
            {{-- ХЛЕБНЫЕ КРОШКИ --}}
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
            @endif
            
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
             
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    {{-- СКРИПТ ДЛЯ ТОГГЛА САЙДБАРА --}}
    <script>
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
        
        // Автоматически открывать подменю если мы на странице поручений
        document.addEventListener('DOMContentLoaded', function() {
            if (window.location.pathname.includes('/orders')) {
                var ordersSubmenu = document.getElementById('ordersSubmenu');
                if (ordersSubmenu) {
                    ordersSubmenu.classList.add('show');
                }
            }
        });
    </script>
    @stack('scripts')
</body>
</html>