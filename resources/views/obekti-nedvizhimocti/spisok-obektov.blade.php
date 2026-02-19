@extends('layouts.app')

<style>
    :root {
        --border-color: #eaecf0;
    }

    /* 1. КОНТЕЙНЕР ТАБЛИЦЫ */
    .table-responsive {
        max-height: 700px;
        overflow-y: auto;
        overflow-x: auto;
        border: 1px solid var(--border-color);
        border-top: none;
        border-radius: 0 0 0.5rem 0.5rem;
    }

    /* 2. ВЕРХНЯЯ ПАНЕЛЬ УПРАВЛЕНИЯ */
    .dt-top-controls {
        background-color: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 0.5rem 0.5rem 0 0;
        padding: 0.75rem 1rem !important;
    }

    /* 3. СТИЛИЗАЦИЯ ВЫПАДАЮЩЕГО СПИСКА КОЛОНОК */
    div.dt-button-collection {
        width: 300px !important;
        z-index: 3000 !important;
    }

    div.dt-button-collection div.dropdown-menu {
        max-height: 150px !important;    /* Ваше ограничение */
        overflow-y: auto !important;     /* Вертикальный скролл */
        overflow-x: auto !important;     /* Горизонтальный скролл */
        display: block !important;
        padding: 0.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
        border: 1px solid var(--border-color) !important;
    }

    div.dt-button-collection button.dt-button {
        min-width: 250px; /* Чтобы появился горизонтальный скролл при узком окне */
        text-align: left !important;
        background: #fff !important;
        border: none !important;
        font-size: 0.85rem !important;
        padding: 8px 12px !important;
        border-radius: 4px;
    }

    div.dt-button-collection button.dt-button.active {
        background: #e7f1ff !important;
        color: #0d6efd !important;
        font-weight: 600;
    }

    /* 4. ТАБЛИЦА */
    #objects-table {
        table-layout: fixed !important;
        width: 100% !important;
        margin: 0 !important;
    }

    #objects-table thead th {
        position: sticky;
        top: 0;
        z-index: 100;
        background-color: #f8f9fa;
        border-bottom: 2px solid var(--border-color);
        text-align: center !important;
        font-size: 0.75rem;
        padding: 12px 5px;
        white-space: nowrap;
    }

    #objects-table th:last-child,
    #objects-table td:last-child {
        position: sticky;
        right: 0;
        z-index: 101;
        background: #fff;
        border-left: 1px solid var(--border-color);
    }

    #objects-table td {
        white-space: nowrap;
        padding: 10px 8px;
        font-size: 0.85rem;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* 5. ПАГИНАЦИЯ И ИНФО */
    .dataTables_wrapper .pagination {
        margin: 0 !important;
        gap: 2px;
    }

    .page-link {
        padding: 0.25rem 0.6rem !important;
        font-size: 0.75rem !important;
        border-radius: 6px !important;
    }

    .form-label-sm { font-size: 0.825rem; color: #6c757d; }
    .dataTables_info { font-size: 0.8rem; margin: 0 !important; padding: 0 !important; color: #667085; }
    /* Убираем старый sticky, если он мешает, и используем этот: */

</style>

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h2 class="h3 mb-0 text-gray-800 font-weight-bold">Отчет о выполненных работах</h2>
                <div class="badge bg-white text-dark border p-2 shadow-sm">
                    Всего найдено: <span id="total-count" class="fw-bold text-primary">...</span>
                </div>
            </div>

            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 collapsed"
                     role="button" data-bs-toggle="collapse" data-bs-target="#collapseFilters">
                    <div class="d-flex align-items-center justify-content-between">
                        <h6 class="m-0 fw-bold text-primary"><i class="bi bi-funnel me-2"></i>Параметры поиска</h6>
                        <i class="bi bi-chevron-down transition-icon"></i>
                    </div>
                </div>
                <div class="collapse" id="collapseFilters">
                    <div class="card-body">
                        <form id="filter-form" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label-sm fw-semibold">Кадастровый номер</label>
                                <input type="text" name="cadastral_number" class="form-control form-control-sm shadow-sm" placeholder="50:09:...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm fw-semibold">Вх. номер</label>
                                <input type="text" name="incoming_number" class="form-control form-control-sm shadow-sm" placeholder="ВХ-...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm fw-semibold">Тип объекта</label>
                                <select class="form-select form-select-sm shadow-sm" name="object_type">
                                    <option value="">Все типы</option>
                                    <option value="ЗУ">Земельный участок</option>
                                    <option value="Здание">Здание</option>
                                    <option value="Сооружение">Сооружение</option>
                                    <option value="Помещение">Помещение</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label-sm fw-semibold">Исполнитель</label>
                                <select class="form-select form-select-sm shadow-sm" name="executor">
                                    <option value="">Все сотрудники</option>
                                    <option value="Иванов">Иванов</option>
                                    <option value="Петров">Петров</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex align-items-end gap-2">
                                <button type="submit" class="btn btn-primary btn-sm px-3 flex-grow-1 shadow-sm">Найти</button>
                                <button type="button" id="reset-filters" class="btn btn-light btn-sm border shadow-sm">Сброс</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm" style="overflow: visible;">
                <div class="table-responsive">
                    <table id="objects-table" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-uppercase fw-bold">
                                <th>Кадастровый номер</th>
                                <th>Тип объекта</th>
                                <th>Вх. номер</th>
                                <th>Вх. дата</th>
                                <th>Номер УРР</th>
                                <th>Дата УРР</th>
                                <th>Тип работ</th>
                                <th>Исполнитель</th>
                                <th>Дата заверш.</th>
                                <th class="text-center">Действия</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.colVis.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#objects-table').DataTable({
        stateSave: true,      // Сохраняем состояние
        stateDuration: 0,     // Храним всегда (localStorage)
        autoWidth: false,
        processing: true,
        serverSide: true,
        scrollX: true,
        // l (length), B (buttons), i (info), p (pagination) - все сверху
        dom: '<"dt-top-controls d-flex flex-wrap justify-content-between align-items-center p-3" <"d-flex align-items-center gap-4" B l i > <"d-flex align-items-center gap-3" p >>rt',

        buttons: [
            {
                extend: 'colvis',
                text: '<i class="bi bi-layout-three-columns me-1"></i> Столбцы',
                className: 'btn btn-light btn-sm border shadow-sm',
                columns: ':not(:last-child)',
                fade: 0
            }
        ],
        language: {
            processing: "Загрузка...",
            lengthMenu: "<span class='form-label-sm fw-semibold text-dark'>Показать по:</span> _MENU_",
            info: "<span class='text-muted small'>_START_–_END_ из _TOTAL_</span>",
            infoEmpty: "Записей нет",
            paginate: {
                previous: "‹",
                next: "›",
                first: "«",
                last: "»"
            }
        },
        ajax: {
            url: "{{ url()->current() }}",
            data: function (d) {
                d.incoming_number = $('input[name=incoming_number]').val();
                d.cadastral_number = $('input[name=cadastral_number]').val();
                d.object_type = $('select[name=object_type]').val();
                d.executor = $('select[name=executor]').val();
            }
        },
        columns: [
            { data: 'kadastroviy_nomer', name: 'kadastroviy_nomer', width: '230px', className: 'text-center fw-medium text-primary' },
            { data: 'tip_obekta_nedvizhimosti', name: 'tip_obekta_nedvizhimosti', width: '180px', className: 'text-center' },
            { data: 'incoming_number', name: 'poruchenie.incoming_number', width: '150px', className: 'text-center' },
            { data: 'incoming_date', name: 'poruchenie.incoming_date', width: '130px', className: 'text-center' },
            { data: 'urr_number', name: 'poruchenie.urr_number', width: '150px', className: 'text-center' },
            { data: 'urr_date', name: 'poruchenie.urr_date', width: '130px', className: 'text-center' },
            { data: 'vid_rabot', name: 'vid_rabot', width: '280px' },
            { data: 'ispolnitel', name: 'ispolnitel', width: '180px', className: 'text-center' },
            { data: 'data_zaversheniya', name: 'data_zaversheniya', width: '140px', className: 'text-center' },
            { data: 'actions', orderable: false, searchable: false, className: 'text-center', width: '100px' }
        ],
        drawCallback: function(settings) {
            $('#total-count').text(settings._iRecordsDisplay);
        }
    });

    // Поиск
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        table.draw();
    });

    // Сброс фильтров
    $('#reset-filters').on('click', function() {
        $('#filter-form')[0].reset();
        $('#filter-form select').val('').trigger('change');
        table.draw();
    });
});
</script>
@endpush
