@extends('layouts.app')

<link href="https://unpkg.com/tabulator-tables@5.5.0/dist/css/tabulator_bootstrap5.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    #report-table { border-radius: 8px; overflow: hidden; border: 1px solid #eaecf0; }
    .tabulator-header { text-transform: uppercase; font-size: 0.75rem !important; background-color: #f8f9fa !important; }
    .tabulator-cell { font-size: 0.85rem !important; vertical-align: middle !important; }
    .tabulator-footer .tabulator-paginator button { margin: 0 2px; border: 1px solid #dee2e6; border-radius: 4px; }
    .tabulator-footer .tabulator-page.active { background-color: #0d6efd !important; color: white !important; }
</style>

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Реестр объектов</h2>
        <div class="badge bg-primary p-2">Всего: <span id="total-count">0</span></div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form id="filter-form" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="cadastral_number" class="form-control form-control-sm" placeholder="Кадастровый номер">
                </div>
                <div class="col-md-2">
                    <input type="text" name="incoming_number" class="form-control form-control-sm" placeholder="Вх. номер">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm">Найти</button>
                    <button type="button" id="reset-filters" class="btn btn-light btn-sm border">Сбросить</button>
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
        // Основные настройки
        height: "600px",
        layout: "fitColumns",
        locale: "ru",
        placeholder: "Нет данных для отображения",

        // ПОЛНАЯ русская локализация
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

        // Настройки пагинации - ИСПРАВЛЕНО
        pagination: "remote",
        paginationMode: "remote",
        paginationSize: 10,
        paginationSizeSelector: [10, 25, 50, 100],
        paginationCounter: "pages", // МЕНЯЕМ НА "pages" для отображения страниц

        // Параметры, отправляемые на сервер
        ajaxParams: function() {
            const form = document.getElementById("filter-form");
            const formData = form ? Object.fromEntries(new FormData(form).entries()) : {};

            // Очищаем пустые значения
            Object.keys(formData).forEach(key => {
                if (!formData[key]) delete formData[key];
            });

            return formData;
        },

        // Обработка ответа от сервера
        ajaxResponse: function(url, params, response) {
            // Обновляем счетчик на странице
            if (document.getElementById('total-count')) {
                document.getElementById('total-count').textContent = response.total || 0;
            }

            return {
                data: response.data,
                last_page: response.last_page,
                total: response.total
            };
        },

        // Настройка колонок
        columns: [
            {
                title: "Кадастровый номер",
                field: "kadastroviy_nomer",
                width: 230,
                frozen: true,
                cssClass: "fw-bold text-primary"
            },
            {
                title: "Тип объекта",
                field: "tip_obekta_nedvizhimosti",
                width: 150
            },
            {
                title: "Вх. номер",
                field: "incoming_number",
                width: 180,
                formatter: function(cell) {
                    const data = cell.getData();
                    return data.poruchenie?.incoming_number || "-";
                }
            },
            {
                title: "Вх. дата",
                field: "incoming_date",
                width: 130,
                formatter: function(cell) {
                    const data = cell.getData();
                    return data.poruchenie?.incoming_date || "-";
                }
            },
            {
                title: "Тип работ",
                field: "vid_rabot",
                width: 200
            },
            {
                title: "Исполнитель",
                field: "ispolnitel",
                width: 180
            },
            {
                title: "Дата заверш.",
                field: "data_zaversheniya",
                width: 140
            },
            {
                title: "Действия",
                field: "id",
                width: 120,
                frozen: true,
                headerSort: false,
                hozAlign: "center",
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
        ]
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
