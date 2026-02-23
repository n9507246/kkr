@extends('layouts.app')

<link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* Основные стили таблицы */
    #report-table { border-radius: 8px; overflow: hidden; border: 1px solid #eaecf0; width: 100%; }
    .tabulator-header { text-transform: uppercase; font-size: 0.75rem !important; background-color: #f8f9fa !important; }
    .tabulator-cell { font-size: 0.85rem !important; vertical-align: middle !important; }

    /* Стили выпадающего списка колонок */
    .dropdown-menu { max-height: 500px !important; overflow-y: auto; min-width: 180px !important;  padding: 12px; z-index: 1060; max-height: 250px !important;}
    .dropdown-item-checkbox { padding: 6px 10px; border-radius: 4px; transition: background 0.2s; cursor: pointer; display: flex; align-items: center; }
    .dropdown-item-checkbox:hover { background-color: #f8f9fa; }
    .dropdown-item-checkbox input { cursor: pointer; margin-right: 12px; width: 16px; height: 16px; }
    .dropdown-item-checkbox label { cursor: pointer; flex: 1; margin: 0; font-size: 0.9rem; user-select: none; }

    .hidden-count-badge { background-color: #dc3545; color: white; padding: 2px 7px; border-radius: 10px; font-size: 0.7rem; margin-left: 5px; }

    /* Плавная анимация панели фильтров */
    #filter-panel { max-height: 0; overflow: hidden; opacity: 0; margin-bottom: 0; transition: all 0.4s ease-in-out; pointer-events: none; }
    #filter-panel.show { max-height: 500px; opacity: 1; margin-bottom: 1.5rem; pointer-events: auto; }
    #toggle-filters.active { background-color: #6c757d; color: white; border-color: #6c757d; }
</style>

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Список поручений УРР</h2>
        <a href="{{ route('porucheniya-urr.sozdat-poruchenie') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Новое поручение
        </a>
    </div>

    <div class="d-flex gap-2 mb-3">
        <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <i class="bi bi-layout-three-columns me-1"></i> Колонки
                <span id="hiddenColumnsCount" class="hidden-count-badge" style="display:none;">0</span>
            </button>
            <div class="spisok-polonok dropdown-menu shadow border-0" aria-labelledby="columnDropdown" >
                <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
                <div id="columnCheckboxes"></div>
                <div class="dropdown-divider"></div>
                <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start" id="resetColumnState">
                    <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
                </button>
            </div>
        </div>

        <button class="btn btn-outline-secondary btn-sm" type="button" id="toggle-filters">
            <i class="bi bi-filter me-1"></i> Фильтры
        </button>
    </div>

    <!-- ПАНЕЛЬ ФИЛЬТРОВ -->
    <div id="filter-panel" class="card border-0 shadow-sm">
        <div class="card-body bg-light border-bottom">
            <form id="filter-form" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Вх. номер</label>
                    <input type="text" name="vhod_nomer" class="form-control form-control-sm" placeholder="Номер...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Вх. дата</label>
                    <input type="date" name="vhod_data" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Номер УРР</label>
                    <input type="text" name="urr_nomer" class="form-control form-control-sm" placeholder="Номер...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Дата УРР</label>
                    <input type="date" name="urr_data" class="form-control form-control-sm">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Исх. номер</label>
                    <input type="text" name="ishod_nomer" class="form-control form-control-sm" placeholder="Номер...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">Исх. дата</label>
                    <input type="date" name="ishod_data" class="form-control form-control-sm">
                </div>
                <div class="col-md-12">
                    <label class="form-label small text-muted fw-bold">Описание</label>
                    <input type="text" name="opisanie" class="form-control form-control-sm" placeholder="Поиск по описанию...">
                </div>

                <div class="col-12 text-end mt-2">
                    <button type="button" id="reset-filters" class="btn btn-light btn-sm border me-2">Сбросить</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Найти</button>
                </div>
            </form>
        </div>
    </div>

    <div id="report-table"></div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <!-- модальное окно как у вас -->
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = '/porucheniya-urr/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.addEventListener("DOMContentLoaded", function() {
    // 1. ЛОГИКА ПЛАВНОГО ПОКАЗА ФИЛЬТРОВ
    const filterBtn = document.getElementById('toggle-filters');
    const filterPanel = document.getElementById('filter-panel');
    const filterForm = document.getElementById("filter-form");
    const storageKey = "porucheniyaFilters_v1";
    const visibilityKey = "porucheniyaFiltersPanelVisible";

    const savedFilters = localStorage.getItem(storageKey);
    let shouldOpenPanel = localStorage.getItem(visibilityKey) === 'true';

    if (savedFilters) {
        try {
            const data = JSON.parse(savedFilters);
            let hasActiveFilters = false;
            Object.keys(data).forEach(key => {
                const input = filterForm.querySelector(`[name="${key}"]`);
                if (input) {
                    input.value = data[key];
                    if (data[key]) hasActiveFilters = true;
                }
            });
            if (hasActiveFilters) shouldOpenPanel = true;
        } catch (e) { console.error(e); }
    }

    if (shouldOpenPanel) {
        filterPanel.classList.add('show');
        filterBtn.classList.add('active');
    }

    filterBtn.addEventListener('click', function() {
        const isOpen = filterPanel.classList.toggle('show');
        this.classList.toggle('active', isOpen);
        localStorage.setItem(visibilityKey, isOpen);
        setTimeout(() => { if(typeof table !== 'undefined') table.redraw(); }, 450);
    });

    // 2. ИНИЦИАЛИЗАЦИЯ ТАБЛИЦЫ - УБИРАЕМ REMOTE PAGINATION!
    var table = new Tabulator("#report-table", {
        height: "600px",
        layout: "fitColumns",
        locale: "ru",
        placeholder: "Нет данных",
        persistence: { columns: ["visible"] },
        persistenceID: "porucheniyaTable_v1",

        ajaxURL: "{{ route('porucheniya-urr.spisok-porucheniy') }}",

        // ВАЖНО: Убираем remote pagination
        // pagination: "remote",  ← ЗАКОММЕНТИРОВАНО

        paginationSize: 20,
        paginationSizeSelector: [10, 20, 50, 100],

        ajaxParams: function() {
            const form = document.getElementById("filter-form");
            return form ? Object.fromEntries(new FormData(form).entries()) : {};
        },

        ajaxResponse: function(url, params, response) {
            console.log("Ответ сервера:", response);
            // Просто возвращаем массив данных
            return response.data || [];
        },

        langs: {
            "ru": {
                "ajax": { "loading": "Загрузка...", "error": "Ошибка загрузки" },
                "pagination": {
                    "page_size": "Показать", "first": "Первая", "last": "Последняя",
                    "prev": "Предыдущая", "next": "Следующая", "all": "Все",
                    "counter": { "showing": "Показано", "of": "из", "rows": "записей", "pages": "страниц" }
                }
            }
        },

        columns: [
            {
                title: "Вх. номер",
                field: "vhod_nomer",
                minWidth: 120,
                frozen: true,
                formatter: function(cell) {
                    const data = cell.getData();
                    const url = `/porucheniya-urr/${data.id}/redaktirovat-poruchenie`;
                    return `<a href="${url}" class="text-primary fw-bold text-decoration-none">${cell.getValue() || '-'}</a>`;
                }
            },
            {
                title: "Вх. дата",
                field: "vhod_data",
                minWidth: 100,
                formatter: function(cell) {
                    return cell.getValue() ? new Date(cell.getValue()).toLocaleDateString('ru-RU') : '-';
                }
            },
            {
                title: "Номер УРР",
                field: "urr_nomer",
                minWidth: 120
            },
            {
                title: "Дата УРР",
                field: "urr_data",
                minWidth: 100,
                formatter: function(cell) {
                    return cell.getValue() ? new Date(cell.getValue()).toLocaleDateString('ru-RU') : '-';
                }
            },
            {
                title: "Исх. номер",
                field: "ishod_nomer",
                minWidth: 120
            },
            {
                title: "Исх. дата",
                field: "ishod_data",
                minWidth: 100,
                formatter: function(cell) {
                    return cell.getValue() ? new Date(cell.getValue()).toLocaleDateString('ru-RU') : '-';
                }
            },
            {
                title: "Описание",
                field: "opisanie",
                minWidth: 200,
                widthGrow: 2
            },
            {
                title: "Действия",
                width: 100,
                hozAlign: "center",
                headerSort: false,
                frozen: true,
                formatter: function(cell) {
                    const data = cell.getData();
                    return `
                        <div class="d-flex gap-2 justify-content-center">
                            <a href="/porucheniya-urr/${data.id}/redaktirovat-poruchenie" class="btn btn-outline-warning btn-sm py-0 px-1" title="Редактировать">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm py-0 px-1" title="Удалить" onclick="confirmDelete(${data.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // 3. УПРАВЛЕНИЕ КОЛОНКАМИ
    table.on("tableBuilt", function() {
        const container = document.getElementById("columnCheckboxes");
        if (!container) return;
        container.innerHTML = "";
        table.getColumns().forEach(column => {
            const def = column.getDefinition();
            if (!def.title || def.field === 'id' || def.title === 'Действия') return;
            const div = document.createElement("div");
            div.className = "dropdown-item-checkbox";
            const isVisible = column.isVisible();
            div.innerHTML = `<input type="checkbox" id="check_${def.field}" ${isVisible ? "checked" : ""}><label for="check_${def.field}">${def.title}</label>`;
            div.querySelector("input").addEventListener("change", function() {
                if (this.checked) column.show(); else column.hide();
                updateHiddenCount();
                setTimeout(() => table.redraw(true), 10);
            });
            container.appendChild(div);
        });
        updateHiddenCount();
    });

    function updateHiddenCount() {
        const checkboxes = document.querySelectorAll("#columnCheckboxes input");
        const hidden = Array.from(checkboxes).filter(i => !i.checked).length;
        const badge = document.getElementById("hiddenColumnsCount");
        if (badge) {
            badge.textContent = hidden;
            badge.style.display = hidden > 0 ? "inline-block" : "none";
        }
    }

    // 4. ФИЛЬТРАЦИЯ И СБРОС
    filterForm.addEventListener("submit", (e) => {
        e.preventDefault();
        const formData = new FormData(filterForm);
        const data = {};
        formData.forEach((value, key) => { if (value) data[key] = value; });
        localStorage.setItem(storageKey, JSON.stringify(data));
        table.setData();
    });

    document.getElementById("reset-filters").addEventListener("click", () => {
        filterForm.reset();
        localStorage.removeItem(storageKey);
        table.setData();
    });

    document.getElementById("resetColumnState").addEventListener("click", () => {
        if(confirm("Сбросить все настройки колонок?")) {
            localStorage.removeItem("tabulator-porucheniyaTable_v1-columns");
            location.reload();
        }
    });
});
</script>
@endpush
@endsection
