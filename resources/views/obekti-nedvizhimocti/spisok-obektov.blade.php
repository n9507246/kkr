@extends('layouts.app')

<link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    /* Основные стили таблицы */
    #report-table { border-radius: 8px; overflow: hidden; border: 1px solid #eaecf0; width: 100%; }
    .tabulator-header { text-transform: uppercase; font-size: 0.75rem !important; background-color: #f8f9fa !important; }
    .tabulator-cell { font-size: 0.85rem !important; vertical-align: middle !important; }
    /* .spisok-polonok { max-height: 500px !important;  } */
    /* Стили выпадающего списка колонок */
    .dropdown-menu { max-height: 500px !important; overflow-y: auto; min-width: 180px !important;  padding: 12px; z-index: 1060; max-height: 250px !important;}
    .dropdown-item-checkbox { padding: 6px 10px; border-radius: 4px; transition: background 0.2s; cursor: pointer; display: flex; align-items: center; }
    .dropdown-item-checkbox:hover { background-color: #f8f9fa; }
    .dropdown-item-checkbox input { cursor: pointer; margin-right: 12px; width: 16px; height: 16px; }
    .dropdown-item-checkbox label { cursor: pointer; flex: 1; margin: 0; font-size: 0.9rem; user-select: none; }

    .hidden-count-badge { background-color: #dc3545; color: white; padding: 2px 7px; border-radius: 10px; font-size: 0.7rem; margin-left: 5px; }

    /* Плавная анимация панели фильтров */
    #filter-panel {
        max-height: 0;
        overflow: hidden;
        opacity: 0;
        margin-bottom: 0;
        transition: all 0.4s ease-in-out;
        pointer-events: none;
    }

    #filter-panel.show {
        max-height: 500px; /* С запасом для контента */
        opacity: 1;
        margin-bottom: 1.5rem;
        pointer-events: auto;
    }

    /* Подсветка активной кнопки фильтров */
    #toggle-filters.active {
        background-color: #6c757d;
        color: white;
        border-color: #6c757d;
    }
</style>

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Все объекты недвижимости</h2>
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
                    <!-- Ряд 1 -->
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Кадастровый номер</label>
                            <input type="text" name="cadastral_number" class="form-control form-control-sm" placeholder="Введите номер...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Тип объекта</label>
                            <select name="tip_obekta_id" class="form-select form-select-sm">
                                <option value="">Все</option>
                                @foreach($tipyObektov as $tip)
                                    <option value="{{ $tip->id }}">{{ $tip->abbreviatura }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Вид работ</label>
                            <select name="vid_rabot_id" class="form-select form-select-sm">
                                <option value="">Все</option>
                                @foreach($vidiRabot as $vid)
                                    <option value="{{ $vid->id }}">{{ $vid->nazvanie }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Исполнитель</label>
                            <input type="text" name="ispolnitel" class="form-control form-control-sm" placeholder="Введите имя...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small text-muted fw-bold">Дата завершения</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="completion_date_start" class="form-control" placeholder="С">
                                <span class="input-group-text px-1">-</span>
                                <input type="date" name="completion_date_end" class="form-control" placeholder="По">
                            </div>
                        </div>

                    <!-- Ряд 2 -->
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Вх. номер</label>
                            <input type="text" name="incoming_number" class="form-control form-control-sm" placeholder="Номер">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Вх. дата</label>
                            <input type="date" name="incoming_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Номер УРР</label>
                            <input type="text" name="urr_number" class="form-control form-control-sm" placeholder="Номер УРР">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small text-muted fw-bold">Дата УРР</label>
                            <input type="date" name="urr_date" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Комментарий</label>
                            <input type="text" name="comment" class="form-control form-control-sm" placeholder="Поиск по тексту...">
                        </div>

                    <!-- Кнопки -->
                        <div class="col-12 text-end mt-2">
                            <button type="button" id="reset-filters" class="btn btn-light btn-sm border me-2">Сбросить</button>
                            <button type="submit" class="btn btn-primary btn-sm px-4">Найти</button>
                        </div>
                </form>
            </div>
        </div>

    <div id="report-table"></div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {

    // 1. ЛОГИКА ПЛАВНОГО ПОКАЗА ФИЛЬТРОВ
    const filterBtn = document.getElementById('toggle-filters');
    const filterPanel = document.getElementById('filter-panel');
    const filterForm = document.getElementById("filter-form");
    const storageKey = "realEstateFilters_v1";
    const visibilityKey = "realEstateFiltersPanelVisible";

    // Восстановление фильтров из localStorage
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
            // Если есть активные фильтры, открываем панель, чтобы пользователь видел контекст
            if (hasActiveFilters) {
                shouldOpenPanel = true;
            }
        } catch (e) {
            console.error("Ошибка загрузки фильтров", e);
        }
    }

    if (shouldOpenPanel) {
        filterPanel.classList.add('show');
        filterBtn.classList.add('active');
    }

    filterBtn.addEventListener('click', function() {
        const isOpen = filterPanel.classList.toggle('show');
        this.classList.toggle('active', isOpen);
        localStorage.setItem(visibilityKey, isOpen);

        // Небольшая задержка, чтобы таблица пересчитала высоту после анимации
        setTimeout(() => {
            if(typeof table !== 'undefined') table.redraw();
        }, 450);
    });

    // Глобальная функция удаления
    window.deleteObject = function(id) {
        if (confirm("Вы действительно хотите удалить объект #" + id + "?")) {
            console.log("Удаление:", id);
        }
    }

    // 2. ИНИЦИАЛИЗАЦИЯ ТАБЛИЦЫ
    const table = new Tabulator("#report-table", {
        height: "78vh",
        layout: "fitColumns",
        locale: "ru",
        placeholder: "Список пуст",

        persistence: { columns: ["visible"] },
        persistenceID: "realEstateTable_vFinal",

        ajaxURL: "{{ url()->current() }}",
        pagination: "remote",
        paginationMode: "remote",
        paginationSize: 20,
        paginationSizeSelector: [10, 20, 50, 100],
        paginationCounter: "rows",

        ajaxParams: function() {
            const form = document.getElementById("filter-form");
            return form ? Object.fromEntries(new FormData(form).entries()) : {};
        },

        ajaxResponse: function(url, params, response) {
            // Обновляем счетчик количества записей
            console.log("Ответ сервера:", response);
            const countElement = document.getElementById('total-count');
            if (countElement) {
                countElement.textContent = response.total || 0;
            }
            return {
                data: response.data || [],
                last_page: response.last_page || 1,
                last_row: response.total || 0
            };
        },
        // Русская локализация
            langs: {
                "ru": {
                    "ajax": {
                        "loading": "Загрузка...",
                        "error": "Ошибка загрузки"
                    },
                    "pagination": {
                        "page_size": "Показать",
                        "first": "Первая",
                        "first_title": "Первая страница",
                        "last": "Последняя",
                        "last_title": "Последняя страница",
                        "prev": "Предыдущая",
                        "prev_title": "Предыдущая страница",
                        "next": "Следующая",
                        "next_title": "Следующая страница",
                        "all": "Все",
                        "counter": {
                            "showing": "Показано",
                            "of": "из",
                            "rows": "записей",
                            "pages": "страниц"
                        }
                    }
                }
            },
        columns: [
            // Кадастровый номер
            { title: "Кадастровый номер", field: "kadastroviy_nomer", minWidth: 200, frozen: true,
                formatter: (cell) => {
                    const d = cell.getData();
                    return `<a href="/obekti-nedvizhimosti/${d.id}/redaktirovat-obekt" class="text-primary fw-bold text-decoration-none">${d.kadastroviy_nomer || '-'}</a>`;
                }
            },

            // Тип объекта (используем аббревиатуру из справочника + подсказка)
            {
                title: "Тип",
                field: "tip_obekta.abbreviatura",
                minWidth: 80,
                hozAlign: "center",
                tooltip: (cell) => cell.getData().tip_obekta?.nazvanie || ""
            },

            // Входящие реквизиты из связи poruchenie
            { title: "Вх. номер", field: "poruchenie.vhod_nomer", minWidth: 120,
                formatter: (cell) => cell.getValue() || "-"
            },
            { title: "Вх. дата", field: "poruchenie.vhod_data", minWidth: 120,
                formatter: (cell) => cell.getValue() || "-"
            },

            // Реквизиты УРР
            { title: "Номер УРР", field: "poruchenie.urr_nomer", minWidth: 120,
                formatter: function(cell) {
                    const d = cell.getData();
                    if (!d.poruchenie) return "-";
                    return `<a href="/porucheniya-urr/${d.poruchenie.id}/redaktirovat-poruchenie" class="text-decoration-none">${d.poruchenie.urr_nomer || ' '}</a>`;
                }
            },
            { title: "Дата УРР", field: "poruchenie.urr_data", minWidth: 120,
                formatter: (cell) => cell.getValue() || "-"
            },

            // Вид работ (из справочника vidi_rabot)
            { title: "Тип работ", field: "vidi_rabot.nazvanie", minWidth: 150,
                formatter: (cell) => cell.getValue() || "-"
            },

            { title: "Исполнитель", field: "ispolnitel", minWidth: 150 },

            // Дата завершения с форматированием
            { title: "Дата заверш.", field: "data_zaversheniya", minWidth: 130,
                formatter: function(cell) {
                    const val = cell.getValue();
                    return val ? new Date(val).toLocaleDateString('ru-RU') : "-";
                }
            },

            // Комментарий (исправлено поле с komentarii на kommentariy)
            { title: "Комментарии", field: "kommentariy", minWidth: 300, widthGrow: 2 },

            // Действия
            { title: "Действия", field: "id", width: 100, headerSort: false, hozAlign: "center", frozen: true,
                formatter: function(cell) {
                    const id = cell.getValue();
                    return `
                        <div class="d-flex gap-2">
                            <a href="/obekti-nedvizhimosti/${id}/redaktirovat-obekt" class="btn btn-outline-warning btn-sm py-0 px-1" title="Редактировать">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button onclick="deleteObject(${id})" class="btn btn-outline-danger btn-sm py-0 px-1" title="Удалить">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ],
    });

    // 3. УПРАВЛЕНИЕ КОЛОНКАМИ
    table.on("tableBuilt", function() {
        const container = document.getElementById("columnCheckboxes");
        if (!container) return;
        container.innerHTML = "";

        table.getColumns().forEach(column => {
            const def = column.getDefinition();
            if (!def.title || def.field === 'id') return;

            const div = document.createElement("div");
            div.className = "dropdown-item-checkbox";
            const isVisible = column.isVisible();

            div.innerHTML = `
                <input type="checkbox" id="check_${def.field}" ${isVisible ? "checked" : ""}>
                <label for="check_${def.field}">${def.title}</label>
            `;

            div.querySelector("input").addEventListener("change", function() {
                if (this.checked) column.show();
                else column.hide();

                updateHiddenCount();

                // Пересчет ширины после скрытия/показа
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

        // Сохраняем текущее состояние фильтров
        const formData = new FormData(filterForm);
        const data = {};
        formData.forEach((value, key) => {
            if (value) data[key] = value;
        });
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
            localStorage.removeItem("tabulator-realEstateTable_vFinal-columns");
            location.reload();
        }
    });
});
</script>
@endpush
