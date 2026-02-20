@props(['user' => null])

<nav id="sidebar" class="bg-primary text-white vh-100 d-flex flex-column" style="width: 17.5rem; min-width: 17.5rem; border-right: 1px solid rgba(255,255,255,0.1);">

    <!-- HEADER -->
    <div class="p-3 border-bottom border-white border-opacity-25">
        <h5 class="mb-1 text-white">
            <i class="bi bi-map me-1"></i> Отдел ККР
        </h5>
        <small class="text-white text-opacity-75">
            Комплексные кадастровые работы
        </small>
    </div>

    <!-- SCROLLABLE MENU -->
    <div class="flex-grow-1 overflow-auto py-2">

        <ul class="list-unstyled mb-0">
            <li class="{{ request()->routeIs('home') ? 'bg-white bg-opacity-25' : '' }}">
                <a href="{{ route('home') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-white text-decoration-none  {{ request()->routeIs('home') ? 'border-white' : 'border-transparent' }} hover-class">
                    <i class="bi bi-grid"></i>
                    Все объекты
                </a>
            </li>

            <li class="{{ request()->routeIs('porucheniya-urr.*') ? 'bg-white bg-opacity-25' : '' }}">
                <a href="{{ route('porucheniya-urr.spisok-porucheniy') }}"
                   class="d-flex align-items-center gap-2 px-3 py-2 text-white text-decoration-none  {{ request()->routeIs('porucheniya-urr.*') ? 'border-white' : 'border-transparent' }} hover-class">
                    <i class="bi bi-file-text"></i>
                    Поручения УРР
                </a>
            </li>
        </ul>

    </div>

    <!-- FOOTER -->
    <div class="p-3 border-top border-white border-opacity-25">
        <div class="d-flex align-items-center">
            <i class="bi bi-person-circle fs-4 me-2 text-white"></i>
            <div class="text-truncate">
                <small class="d-block text-white text-opacity-75">
                    {{ $user['role'] ?? 'Администратор' }}
                </small>
                <strong class="text-white">{{ $user['name'] ?? 'Иванов И.И.' }}</strong>
            </div>
        </div>
    </div>

</nav>

<style>
/* Hover эффект */
.hover-class:hover {
    background-color: rgba(255, 255, 255, 0.15) !important;
    border-left-color: white !important;
}

.border-transparent {
    border-left-color: transparent;
}

/* Кастомный скроллбар */
.overflow-auto::-webkit-scrollbar {
    width: 0.5rem;
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 0.5rem;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Убираем обводку при фокусе */
a:focus {
    outline: none;
    box-shadow: none;
}
</style>
