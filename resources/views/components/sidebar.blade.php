@props(['user' => null])

<nav id="sidebar"
     class="bg-primary text-white vh-100 d-flex flex-column shadow"
     style="width: 17.5rem; min-width: 17.5rem; border-right: 1px solid rgba(255,255,255,0.1); transition: width 0.3s ease, min-width 0.3s ease;">

    <!-- HEADER -->
    <div class="p-3 border-bottom border-white border-opacity-25 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-3">
            <i class="bi bi-map fs-4 text-white"></i>
            <div class="sidebar-title">
                <h5 class="mb-0 text-white fw-semibold">Отдел ККР</h5>
                <small class="text-white text-opacity-75">Комплексные кадастровые работы</small>
            </div>
        </div>

        <button id="toggleSidebar"
                type="button"
                class="btn btn-sm p-0 border-0 text-white-50 hover-white"
                title="Свернуть/развернуть">
            <i class="bi bi-chevron-left fs-6 toggle-icon"></i>
        </button>
    </div>

    <!-- MENU -->
    <div class="flex-grow-1 overflow-auto py-3">
        <ul class="list-unstyled mb-0">

            <li class="mb-1 px-2">
                <a href="{{ route('home') }}"
                   class="d-flex align-items-center gap-3 px-3 py-2 text-white text-decoration-none rounded-3 {{ request()->routeIs('home') ? 'bg-white bg-opacity-25' : '' }} hover-class">
                    <i class="bi bi-grid fs-5"></i>
                    <span class="nav-text">Объекты недвижимости</span>
                </a>
            </li>

            <li class="mb-1 px-2">
                <a href="{{ route('porucheniya-urr.spisok-porucheniy') }}"
                   class="d-flex align-items-center gap-3 px-3 py-2 text-white text-decoration-none rounded-3 {{ request()->routeIs('porucheniya-urr.*') ? 'bg-white bg-opacity-25' : '' }} hover-class">
                    <i class="bi bi-file-text fs-5"></i>
                    <span class="nav-text">Поручения УРР</span>
                </a>
            </li>

        </ul>
    </div>

    <!-- FOOTER -->
    <div class="p-3 border-top border-white border-opacity-25">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-4 text-white"></i>
                <span class="nav-text text-white">{{ auth()->user()->name ?? 'Иванов И.И.' }}</span>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit"
                        class="btn p-0 border-0 text-white-50 hover-white"
                        title="Выйти">
                    <i class="bi bi-box-arrow-right fs-6"></i>
                </button>
            </form>
        </div>
    </div>

</nav>

<style>
/* COLLAPSED STATE */
#sidebar.collapsed {
    width: 5.5rem !important;
    min-width: 5.5rem !important;
}

#sidebar.collapsed .sidebar-title,
#sidebar.collapsed .nav-text {
    display: none;
}

#sidebar.collapsed .d-flex.align-items-center.gap-3 {
    gap: 0 !important;
}

#sidebar.collapsed .d-flex.align-items-center.gap-3 i {
    margin: 0 auto;
}

/* Hover */
.hover-class:hover {
    background-color: rgba(255, 255, 255, 0.15) !important;
}

.hover-white:hover {
    color: white !important;
}

/* Иконка поворота */
.toggle-icon {
    display: inline-block;
    transition: transform 0.6s ease;
}

.toggle-icon.rotated {
    transform: rotate(180deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggleSidebar');
    const icon = toggleBtn.querySelector('.toggle-icon');

    // восстановление состояния
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';

    if (isCollapsed) {
        sidebar.classList.add('collapsed');
        icon.classList.add('rotated');
    }

    toggleBtn.addEventListener('click', function () {

        sidebar.classList.toggle('collapsed');
        icon.classList.toggle('rotated');

        localStorage.setItem(
            'sidebarCollapsed',
            sidebar.classList.contains('collapsed')
        );
    });
});
</script>
