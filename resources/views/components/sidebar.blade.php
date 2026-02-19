{{-- resources/views/components/sidebar.blade.php --}}

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

@props(['user' => null])

<nav id="sidebar" {{ $attributes }}>
    <div class="sidebar-header">
        <h3><i class="bi bi-map"></i> Отдел ККР</h3>
        <p class="small text-white-50 mb-0">Комплексные кадастровые работы</p>
    </div>

    <ul class="list-unstyled components">

        {{-- ВСЕ РАБОТЫ --}}
        <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
            <a href="{{ route('home') }}">
                <i class="bi bi-grid"></i>
                Все объекты
            </a>
        </li>

        {{-- ПОРУЧЕНИЯ УРР --}}
        <li class="{{ request()->routeIs('porucheniya-urr.*') ? 'active' : '' }}">
            <a href="{{ route('porucheniya-urr.spisok-porucheniy') }}">
                <i class="bi bi-file-text"></i>
                Поручения УРР
                <span class="badge rounded-pill">5</span>
            </a>
        </li>



    </ul>

    {{-- ИНФОРМАЦИЯ О ПОЛЬЗОВАТЕЛЕ --}}
    <div class="sidebar-footer p-3 border-top border-white-50">
        <div class="d-flex align-items-center">
            <i class="bi bi-person-circle fs-4 me-2"></i>
            <div>
                <small class="d-block text-white-50">
                    {{ $user['role'] ?? 'Администратор' }}
                </small>
                <strong>{{ $user['name'] ?? 'Иванов И.И.' }}</strong>
            </div>
        </div>
    </div>
</nav>
