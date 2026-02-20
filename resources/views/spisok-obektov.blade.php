@extends('layouts.app')

<link href="https://unpkg.com/tabulator-tables/dist/css/tabulator.min.css" rel="stylesheet">
<script type="text/javascript" src="https://unpkg.com/tabulator-tables/dist/js/tabulator.min.js"></script>

<style>
    #report-table { border-radius: 8px; overflow: hidden; border: 1px solid #eaecf0; }
    .tabulator-header { text-transform: uppercase; font-size: 0.75rem !important; background-color: #f8f9fa !important; }
    .tabulator-cell { font-size: 0.85rem !important; vertical-align: middle !important; }
    .tabulator-footer .tabulator-paginator button { margin: 0 2px; border: 1px solid #dee2e6; border-radius: 4px; }
    .tabulator-footer .tabulator-page.active { background-color: #0d6efd !important; color: white !important; }

    /* Стили для выпадающего списка */
    .dropdown-menu {
        max-height: 400px;
        overflow-y: auto;
        padding: 10px;
        min-width: 250px;
    }
    .dropdown-item-checkbox {
        padding: 0.5rem 1rem;
        border-radius: 4px;
        transition: background-color 0.2s;
        cursor: pointer;
    }
    .dropdown-item-checkbox:hover {
        background-color: #f8f9fa;
    }
    .dropdown-item-checkbox input[type="checkbox"] {
        margin-right: 10px;
        cursor: pointer;
    }
    .dropdown-item-checkbox label {
        cursor: pointer;
        flex: 1;
        margin: 0;
    }
    .column-controls {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }
    .hidden-count-badge {
        background-color: #6c757d;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        margin-left: 8px;
    }

    #report-table  {
        max-width: 1760px;
    }


</style>

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Реестр объектов</h2>
        <div class="badge bg-primary p-2">Всего: <span id="total-count">0</span></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form id="filter-form" class="row g-3 p-0 m-0 ">
                <div class="col-md-3 p-0 ms-0 my-0 me-3">
                    <input type="text" name="cadastral_number" class="form-control form-control-sm" placeholder="Кадастровый номер">
                </div>
                <div class="col-md-2 p-0 ms-0 my-0 me-3">
                    <input type="text" name="incoming_number" class="form-control form-control-sm" placeholder="Вх. номер">
                </div>
                <div class="col-md-3 p-0 m-0">
                    <button type="submit" class="btn btn-primary btn-sm">Найти</button>
                    <button type="button" id="reset-filters" class="btn btn-light btn-sm border">Сбросить</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Панель управления столбцами
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body py-2">
            <div class="d-flex justify-content-between align-items-center">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                            id="columnDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-layout-three-columns"></i> Колонки
                        <span class="hidden-count-badge" id="hiddenColumnsCount">0</span>
                    </button>
                    <div class="dropdown-menu shadow" aria-labelledby="columnDropdown">

                        <div id="columnCheckboxes">
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="kadastroviy_nomer" id="col_kadastr" checked>
                                <label for="col_kadastr">Кадастровый номер</label>
                            </div>
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="tip_obekta_nedvizhimosti" id="col_tip" checked>
                                <label for="col_tip">Тип объекта</label>
                            </div>
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="incoming_number" id="col_in" checked>
                                <label for="col_in">Вх. номер</label>
                            </div>
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="incoming_date" id="col_indate" checked>
                                <label for="col_indate">Вх. дата</label>
                            </div>
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="vid_rabot" id="col_vid" checked>
                                <label for="col_vid">Тип работ</label>
                            </div>
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="ispolnitel" id="col_isp" checked>
                                <label for="col_isp">Исполнитель</label>
                            </div>
                            <div class="dropdown-item-checkbox d-flex align-items-center">
                                <input type="checkbox" class="col-checkbox" data-column="data_zaversheniya" id="col_date" checked>
                                <label for="col_date">Дата заверш.</label>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>


                        <div class="px-2 pt-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm w-100" id="resetColumnState">
                                <i class="bi bi-arrow-counterclockwise"></i> Сбросить настройки
                            </button>
                        </div>
                    </div>
                </div>

                <small class="text-muted">
                    <i class="bi bi-info-circle"></i> Выберите колонки для отображения
                </small>
            </div>
        </div>
    </div>
    --}}
    <div id="report-table"></div>
</div>
@endsection

@push('scripts')
<script type="text/javascript" src="https://unpkg.com/tabulator-tables@5.5.0/dist/js/tabulator.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Глобальные функции для действий
    window.editObject = function(id) {
        alert("Редактировать: " + id);
    }

    window.deleteObject = function(id) {
        if (confirm("Удалить объект " + id + "?")) {
            alert("Удаление: " + id);
        }
    }

    // Инициализация таблицы
    var table = new Tabulator("#report-table", {
        height: "600px",
        layout: "fitColumns",  // Это важно для автоматического растягивания
        locale: "ru",
        placeholder: "Нет данных для отображения",
        /* layoutColumnsOnNewData:true,*/
        persistence: true, // включаем сохранение
        persistence: {
            sort: true,
            filter: true,
            headerFilter: true,
            page: true,
            columns: ["visible", "frozen"] // СОХРАНЯЕМ ТОЛЬКО ВИДИМОСТЬ И ЗАМОРОЗКУ, НО НЕ ШИРИНУ
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

        // AJAX настройки
        ajaxURL: "{{ url()->current() }}",
        ajaxConfig: "GET",
        ajaxContentType: "json",

        // Настройки пагинации
        pagination: "remote",
        paginationMode: "remote",
        paginationSize: 20,
        paginationSizeSelector: [10, 20, 50, 100],
        paginationCounter: "rows",

        // Параметры, отправляемые на сервер
        ajaxParams: function() {
            const form = document.getElementById("filter-form");
            const formData = form ? Object.fromEntries(new FormData(form).entries()) : {};
            Object.keys(formData).forEach(key => {
                if (!formData[key]) delete formData[key];
            });
            return formData;
        },

        // Обработка ответа от сервера
        ajaxResponse: function(url, params, response) {
            if (document.getElementById('total-count')) {
                document.getElementById('total-count').textContent = response.total || 0;
            }
            return {
                    data: response.data,
                    last_page: response.last_page,
                    last_row: response.total  // <-- ИСПРАВЛЕНИЕ: используйте last_row вместо total
                };
        },

        // Настройка колонок
        columns: [
            { title: "Кадастровый номер", field: "kadastroviy_nomer", width: 230, frozen: true, cssClass: "fw-bold text-primary"},
            { title: "Тип объекта", field: "tip_obekta_nedvizhimosti", },
            { title: "Вх. номер", field: "incoming_number",
                formatter: function(cell) {
                    const data = cell.getData();
                    return data.poruchenie?.incoming_number || "-";
                }
            },
            { title: "Вх. дата", field: "incoming_date",
                formatter: function(cell) {
                    const data = cell.getData();
                    return data.poruchenie?.incoming_date || "-";
                }
            },
            { title: "Тип работ", field: "vid_rabot" },
            { title: "Исполнитель", field: "ispolnitel"},
            { title: "Дата заверш.", field: "data_zaversheniya" },
            { title: "Действия", field: "id", width: 120, frozen: true, headerSort: false, hozAlign: "center",
                formatter: function(cell) {
                    const id = cell.getValue();
                    return `
                        <div class="d-flex gap-1 justify-content-center">
                            <button class="btn btn-outline-warning btn-sm p-1" onclick="editObject(${id})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-outline-danger btn-sm p-1" onclick="deleteObject(${id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>`;
                }
            }
        ],

        // Событие после построения таблицы
        tableBuilt: function() {
            // Загружаем сохраненное состояние ПОСЛЕ построения таблицы
            setTimeout(() => {
                loadColumnState();
            }, 100);
        }
    });

    //
    // ==== ФУНКЦИИ ДЛЯ УПРАВЛЕНИЯ СТОЛБЦАМИ ====
    //
    // Сохранение состояния в localStorage
        function saveColumnState() {
            const state = {};
            document.querySelectorAll('.col-checkbox').forEach(checkbox => {
                state[checkbox.dataset.column] = checkbox.checked;
            });
            localStorage.setItem('columnVisibility', JSON.stringify(state));
            updateHiddenColumnsCount();
        }

    // Загрузка состояния из localStorage
        function loadColumnState() {
            const saved = localStorage.getItem('columnVisibility');

            // Если есть сохраненное состояние
            if (saved) {
                try {
                    const state = JSON.parse(saved);

                    // Блокируем перерисовку для производительности
                    table.blockRedraw();

                    // Применяем сохраненное состояние
                    Object.keys(state).forEach(field => {
                        const checkbox = document.querySelector(`.col-checkbox[data-column="${field}"]`);
                        if (checkbox) {
                            checkbox.checked = state[field];
                            if (state[field]) {
                                table.showColumn(field);
                            } else {
                                table.hideColumn(field);
                            }
                        }
                    });

                    table.restoreRedraw();

                    // ВАЖНО: Пересчитываем ширину колонок после применения состояния
                    setTimeout(() => {
                        table.redraw(true); // true - пересчитать ширину
                    }, 50);

                } catch (e) {
                    console.error('Ошибка загрузки состояния:', e);
                    // В случае ошибки показываем все колонки
                    resetToDefault();
                }
            } else {
                // Если нет сохраненного состояния, показываем все колонки
                resetToDefault();
            }

            updateHiddenColumnsCount();
        }

    // Сброс к настройкам по умолчанию (все колонки видны)
        function resetToDefault() {
            table.blockRedraw();

            document.querySelectorAll('.col-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                table.showColumn(checkbox.dataset.column);
            });

            table.restoreRedraw();

            // Пересчитываем ширину
            setTimeout(() => {
                table.redraw(true);
            }, 50);
        }

    // Обновление счетчика скрытых столбцов
        function updateHiddenColumnsCount() {
            const hiddenCount = document.querySelectorAll('.col-checkbox:not(:checked)').length;
            const badge = document.getElementById('hiddenColumnsCount');
            if (badge) {
                badge.textContent = hiddenCount;
                badge.style.display = hiddenCount > 0 ? 'inline-block' : 'none';
            }
        }

    //
    // ==== ОБРАБОТЧИКИ СОБЫТИЙ ====
    //
    // Обработка чекбоксов столбцов
        document.querySelectorAll('.col-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const column = this.dataset.column;

                table.blockRedraw();

                if (this.checked) {
                    table.showColumn(column);
                } else {
                    table.hideColumn(column);
                }

                table.restoreRedraw();

                // ВАЖНО: Пересчитываем ширину колонок после изменения
                setTimeout(() => {
                    table.redraw(true); // true - пересчитать ширину с учетом видимых колонок
                }, 50);

                // Сохраняем состояние после изменения
                saveColumnState();
            });
        });

    // Кнопка сброса настроек
        document.getElementById('resetColumnState')?.addEventListener('click', function() {
            // Удаляем сохраненное состояние
            localStorage.removeItem('columnVisibility');

            // Сбрасываем все чекбоксы на "показано"
            table.blockRedraw();

            document.querySelectorAll('.col-checkbox').forEach(checkbox => {
                checkbox.checked = true;
                table.showColumn(checkbox.dataset.column);
            });

            table.restoreRedraw();

            // Пересчитываем ширину
            setTimeout(() => {
                table.redraw(true);
            }, 50);

            updateHiddenColumnsCount();
        });

    // Обработка формы фильтрации
        const filterForm = document.getElementById("filter-form");
        if (filterForm) {
            filterForm.addEventListener("submit", function(e) {
                e.preventDefault();
                table.setPage(1);
                table.setData();
            });
        }

    // Сброс фильтров
        const resetBtn = document.getElementById("reset-filters");
        if (resetBtn) {
            resetBtn.addEventListener("click", function() {
                filterForm.reset();
                table.setPage(1);
                table.setData();
            });
        }
});
</script>
@endpush
