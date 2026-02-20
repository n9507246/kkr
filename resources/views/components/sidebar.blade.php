@props(['user' => null])

<nav id="sidebar" class="bg-primary text-white vh-100 d-flex flex-column shadow" style="width: 17.5rem; min-width: 17.5rem; border-right: 1px solid rgba(255,255,255,0.1);">

    <!-- HEADER с логотипом -->
    <div class="p-4 border-bottom border-white border-opacity-25">
        <div class="d-flex align-items-center gap-4">
            <div class="">
                <i class="bi bi-map fs-4 text-white"></i>
            </div>
            <div>
                <h5 class="mb-0 text-white fw-semibold">Отдел ККР</h5>
                <small class="text-white text-opacity-75">Комплексные кадастровые работы</small>
            </div>
        </div>
    </div>

    <!-- МЕНЮ -->
    <div class="flex-grow-1 overflow-auto py-3">
        <ul class="list-unstyled mb-0">
            <!-- Объекты недвижимости -->
            <li class="mb-1 px-2">
                <a href="{{ route('home') }}"
                   class="d-flex align-items-center gap-3 px-3 py-2 text-white text-decoration-none rounded-3 {{ request()->routeIs('home') ? 'bg-white bg-opacity-25' : '' }} hover-class">
                    <i class="bi bi-grid fs-5"></i>
                    <span>Объекты недвижимости</span>
                </a>
            </li>

            <!-- Поручения УРР -->
            <li class="mb-1 px-2">
                <a href="{{ route('porucheniya-urr.spisok-porucheniy') }}"
                   class="d-flex align-items-center gap-3 px-3 py-2 text-white text-decoration-none rounded-3 {{ request()->routeIs('porucheniya-urr.*') ? 'bg-white bg-opacity-25' : '' }} hover-class">
                    <i class="bi bi-file-text fs-5"></i>
                    <span>Поручения УРР</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- ФУТЕР - пользователь и выход в один ряд -->
    <div class="p-3 border-top border-white border-opacity-25">
        <div class="d-flex align-items-center justify-content-between">
            <!-- Иконка и имя пользователя -->
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-person-circle fs-4"></i>
                <div>
                    <div class="small text-white text-opacity-75">{{ auth()->user()->name ?? 'Иванов И.И.' }}</div>
                </div>
            </div>

            <!-- Кнопка выхода (только иконка) -->
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn p-0 border-0 text-white" title="Выйти">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </button>
            </form>
        </div>
    </div>

</nav>

<style>
.hover-class {
    transition: all 0.2s ease;
}

.hover-class:hover {
    background-color: rgba(255, 255, 255, 0.2) !important;
}

/* Скроллбар */
.overflow-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-auto::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

.overflow-auto::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 6px;
}

.overflow-auto::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}
</style>
