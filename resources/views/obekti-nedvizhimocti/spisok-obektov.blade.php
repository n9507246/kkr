@extends('layouts.app')

@section('content')
<div class="">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Все объекты недвижимости</h2>
    </div>

    <div class="d-flex gap-2 mb-3">
        <div class="dropdown" role="controll_column_visiable" to-smart-table="report-table">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                <i class="bi bi-layout-three-columns me-1"></i> Колонки
                <span id="hiddenColumnsCount" class="hidden-count-badge" style="display:none;">0</span>
            </button>
            <div class="spisok-polonok dropdown-menu shadow border-0" aria-labelledby="columnDropdown">
                <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
                <div id="columnCheckboxes" to-smart-table="report-table" role="controll_column_visiable_list"></div>
                <div class="dropdown-divider"></div>
                <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start" id="resetColumnState" to-smart-table="report-table" role="reset_column_visibility">
                    <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
                </button>
            </div>
        </div>

        <!-- Кнопка сворачивания фильтров с иконкой -->
        <button class="btn btn-outline-secondary btn-sm" type="button" id="toggle-filters" data-bs-toggle="collapse" data-bs-target="#filterPanel" aria-expanded="true" aria-controls="filterPanel">
            <i class="bi bi-filter me-1"></i> Фильтры
            <i class="bi bi-chevron-down ms-1" id="filterToggleIcon"></i>
        </button>

        <!-- Кнопка экспорта в Excel -->
        <button id="export-excel-btn" class="btn btn-success btn-sm" type="button">
            <i class="bi bi-file-earmark-excel me-1"></i> Экспорт в Excel
        </button>
    </div>

    <!-- ПАНЕЛЬ ФИЛЬТРОВ - класс show будет установлен динамически -->
    <div class="collapse" id="filterPanel">
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-body bg-light border-bottom">
                <form id="filter-form" class="row g-3" to-smart-table="report-table" role="fiters_table">
                    <!-- Ряд 1 -->
                    <div class="col-md-3">
                        <label class="form-label small text-muted fw-bold">Кадастровый номер</label>
                        <input type="text" name="kadastroviy_nomer" class="form-control form-control-sm" placeholder="Введите номер...">
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
                            <input type="date" name="data_zaversheniya_start" class="form-control" placeholder="С">
                            <span class="input-group-text px-1">-</span>
                            <input type="date" name="data_zaversheniya_end" class="form-control" placeholder="По">
                        </div>
                    </div>

                    <!-- Ряд 2 -->
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Вх. номер</label>
                        <input type="text" name="vhod_nomer" class="form-control form-control-sm" placeholder="Номер">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Вх. дата</label>
                        <input type="date" name="vhod_data" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Номер УРР</label>
                        <input type="text" name="urr_nomer" class="form-control form-control-sm" placeholder="Номер УРР">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted fw-bold">Дата УРР</label>
                        <input type="date" name="urr_data" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted fw-bold">Комментарий</label>
                        <input type="text" name="kommentariy" class="form-control form-control-sm" placeholder="Поиск по тексту...">
                    </div>

                    <!-- Кнопки -->
                    <div class="col-12 text-end mt-2">
                        <button type="button" id="reset-filters" class="btn btn-light btn-sm border me-2">Сбросить</button>
                        <button type="submit" class="btn btn-primary btn-sm px-4">Найти</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="report-table"></div>
</div>
@endsection

@push('scripts')
<script type="module">
    import { create_smart_table } from '{{ Vite::asset('resources/js/app.js') }}';

    document.addEventListener('DOMContentLoaded', function() {
        const table = create_smart_table({
            // debug: true,
            height: '80vh',
            id: 'report-table',
            ajaxURL: "{{ route('obekti-nedvizhimosti.spisok-obektov') }}",
            export_to_excel: true,
            columns: [
                // Кадастровый номер
                {
                    title: "Кадастровый номер",
                    field: "kadastroviy_nomer",
                    minWidth: 200,
                    frozen: true,
                    sorter: "string",
                    formatter: (cell) => {
                        const d = cell.getData();
                        return `<a href="/obekti-nedvizhimosti/${d.id}/redaktirovat-obekt" class="text-primary fw-bold text-decoration-none">${d.kadastroviy_nomer || '-'}</a>`;
                    }
                },

                // Тип объекта
                {
                    title: "Тип",
                    field: "tip_obekta.abbreviatura",
                    minWidth: 80,
                    hozAlign: "center",
                    sorter: "string",
                    tooltip: (cell) => cell.getData().tip_obekta?.nazvanie || ""
                },

                // Входящие реквизиты
                {
                    title: "Вх. номер",
                    field: "poruchenie.vhod_nomer",
                    minWidth: 120,
                    sorter: "string",
                    formatter: (cell) => cell.getValue() || "-"
                },
                {
                    title: "Вх. дата",
                    field: "poruchenie.vhod_data",
                    minWidth: 120,
                    sorter: "date",
                    sorterParams: {
                        format: "YYYY-MM-DD"
                    },
                    formatter: (cell) => {
                        const val = cell.getValue();
                        return val ? new Date(val).toLocaleDateString('ru-RU') : "-";
                    }
                },

                // Реквизиты УРР
                {
                    title: "Номер УРР",
                    field: "poruchenie.urr_nomer",
                    minWidth: 120,
                    sorter: "string",
                    formatter: function(cell) {
                        const d = cell.getData();
                        if (!d.poruchenie) return "-";
                        return `<a href="/porucheniya-urr/${d.poruchenie.id}/redaktirovat-poruchenie" class="text-decoration-none">${d.poruchenie.urr_nomer || ' '}</a>`;
                    }
                },
                {
                    title: "Дата УРР",
                    field: "poruchenie.urr_data",
                    minWidth: 120,
                    sorter: "date",
                    sorterParams: {
                        format: "YYYY-MM-DD"
                    },
                    formatter: (cell) => {
                        const val = cell.getValue();
                        return val ? new Date(val).toLocaleDateString('ru-RU') : "-";
                    }
                },

                // Вид работ
                {
                    title: "Тип работ",
                    field: "vidi_rabot.nazvanie",
                    minWidth: 150,
                    sorter: "string",
                    formatter: (cell) => cell.getValue() || "-"
                },

                // Исполнитель
                {
                    title: "Исполнитель",
                    field: "ispolnitel",
                    minWidth: 150,
                    sorter: "string"
                },

                // Дата завершения
                {
                    title: "Дата заверш.",
                    field: "data_zaversheniya",
                    minWidth: 130,
                    sorter: "date",
                    sorterParams: {
                        format: "YYYY-MM-DD"
                    },
                    formatter: function(cell) {
                        const val = cell.getValue();
                        return val ? new Date(val).toLocaleDateString('ru-RU') : "-";
                    }
                },

                // Комментарий
                {
                    title: "Комментарии",
                    field: "kommentariy",
                    minWidth: 300,
                    widthGrow: 2,
                    sorter: "string"
                },
            ],
            controll_column_visiable: true,
            apply_filters: true,
        });

        // Обработчик для кнопки экспорта в Excel
        document.getElementById('export-excel-btn').addEventListener('click', function() {
            if (table && typeof table.download === 'function') {
                table.download('xlsx', 'obekti-nedvizhimosti.xlsx', {
                    sheet: {
                        name: 'Объекты недвижимости'
                    }
                });
            } else {
                // Альтернативный метод если download не работает
                console.log('Попытка экспорта через таблицу');
                // Можно добавить дополнительную логику экспорта
            }
        });

        // Дополнительный JavaScript для управления иконкой при сворачивании/разворачивании
        const filterPanel = document.getElementById('filterPanel');
        const toggleButton = document.getElementById('toggle-filters');
        const filterIcon = document.getElementById('filterToggleIcon');

        if (filterPanel && toggleButton && filterIcon) {
            // Сохранение состояния при изменении
            filterPanel.addEventListener('hidden.bs.collapse', function () {
                filterIcon.classList.remove('bi-chevron-up');
                filterIcon.classList.add('bi-chevron-down');
                toggleButton.setAttribute('aria-expanded', 'false');
                localStorage.setItem('filterPanelState', 'hidden');
            });

            filterPanel.addEventListener('shown.bs.collapse', function () {
                filterIcon.classList.remove('bi-chevron-down');
                filterIcon.classList.add('bi-chevron-up');
                toggleButton.setAttribute('aria-expanded', 'true');
                localStorage.setItem('filterPanelState', 'shown');
            });

            // Инициализация состояния из localStorage
            const savedState = localStorage.getItem('filterPanelState');
            
            // По умолчанию показываем панель (если нет сохраненного состояния или оно 'shown')
            if (savedState === 'hidden') {
                // Скрываем панель
                const collapse = new bootstrap.Collapse(filterPanel, {
                    toggle: false
                });
                collapse.hide();
                filterIcon.classList.remove('bi-chevron-up');
                filterIcon.classList.add('bi-chevron-down');
                toggleButton.setAttribute('aria-expanded', 'false');
            } else {
                // Показываем панель (по умолчанию)
                filterPanel.classList.add('show');
                filterIcon.classList.remove('bi-chevron-down');
                filterIcon.classList.add('bi-chevron-up');
                toggleButton.setAttribute('aria-expanded', 'true');
                
                // Если нет сохраненного состояния, устанавливаем 'shown' по умолчанию
                if (!savedState) {
                    localStorage.setItem('filterPanelState', 'shown');
                }
            }
        }
    });
</script>
@endpush