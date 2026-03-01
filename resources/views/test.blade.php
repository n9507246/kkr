
@php $id_table = 'test-smart-table'; @endphp

<x-layout>

    <x-smart-table.component :id="$id_table">
        <x-slot:control-panel>
            <div class='d-flex gap-2 mb-2'>
                <x-smart-table.column-controller>

                    <x-slot:btn-controll>
                        <x-smart-table.column-controller-btn/>
                    </x-slot:btn-controll>

                    <x-slot:dropdown-menu>                        
                        <x-smart-table.column-controller-dropdown :id="$id_table" style='min-width: 200px !important'/>
                    </x-slot:dropdown-menu>
                    
                </x-smart-table.column-controller>
                <x-smart-table.filter-panel-btn :id="$id_table"/>
            </div>
            <x-smart-table.filter-panel :id="$id_table">
                <x-slot:filters>
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
                    
                </x-slot:filters>
            </x-smart-table.filter-panel>
        </x-slot:control-panel>
    </x-smart-table.component>


    <x-slot:scripts>
        <script type="module">
            import { create_smart_table } from '{{ Vite::asset('resources/js/app.js') }}';

            document.addEventListener('DOMContentLoaded', function() {
                const tableId = '{{ $id_table }}';
                const panelId = `filterPanel_${tableId}`;
                const panelStateKey = `smart-table:${tableId}:filter-panel-open`;
                const panelEl = document.getElementById(panelId);

                if (panelEl) {
                    const savedState = localStorage.getItem(panelStateKey);
                    const shouldOpen = savedState === null ? true : savedState === 'true';

                    if (typeof bootstrap !== 'undefined') {
                        const collapse = new bootstrap.Collapse(panelEl, { toggle: false });
                        if (shouldOpen) {
                            collapse.show();
                        } else {
                            collapse.hide();
                        }
                    } else {
                        panelEl.classList.toggle('show', shouldOpen);
                    }

                    panelEl.addEventListener('shown.bs.collapse', () => {
                        localStorage.setItem(panelStateKey, 'true');
                    });

                    panelEl.addEventListener('hidden.bs.collapse', () => {
                        localStorage.setItem(panelStateKey, 'false');
                    });
                }

                const table = create_smart_table({
                    // debug: true,
                    height: '80vh',
                    id: tableId,
                    ajaxURL: "{{ route('obekti-nedvizhimosti.spisok-obektov') }}",
                    export_to_excel: true,
                    columns: [
                        { title: "Кадастровый номер",
                            field: "kadastroviy_nomer",
                            minWidth: 200,
                            frozen: true,
                            sorter: "string",
                            formatter: (cell) => {
                                const d = cell.getData();
                                return `<a href="/obekti-nedvizhimosti/${d.id}/redaktirovat-obekt" class="text-primary fw-bold text-decoration-none">${d.kadastroviy_nomer || '-'}</a>`;
                            }
                        },
                        { title: "Тип",
                            field: "tip_obekta.abbreviatura",
                            minWidth: 80,
                            hozAlign: "center",
                            sorter: "string",
                            tooltip: (cell) => cell.getData().tip_obekta?.nazvanie || ""
                        },
                        { title: "Вх. номер",
                            field: "poruchenie.vhod_nomer",
                            minWidth: 120,
                            sorter: "string",
                            formatter: (cell) => cell.getValue() || "-"
                        },
                        { title: "Вх. дата",
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
                        { title: "Номер УРР",
                            field: "poruchenie.urr_nomer",
                            minWidth: 120,
                            sorter: "string",
                            formatter: function(cell) {
                                const d = cell.getData();
                                if (!d.poruchenie) return "-";
                                return `<a href="/porucheniya-urr/${d.poruchenie.id}/redaktirovat-poruchenie" class="text-decoration-none">${d.poruchenie.urr_nomer || ' '}</a>`;
                            }
                        },
                        { title: "Дата УРР",
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
                        { title: "Тип работ",
                            field: "vidi_rabot.nazvanie",
                            minWidth: 150,
                            sorter: "string",
                            formatter: (cell) => cell.getValue() || "-"
                        },
                        { title: "Исполнитель",
                            field: "ispolnitel",
                            minWidth: 150,
                            sorter: "string"
                        },
                        { title: "Дата заверш.",
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
                        { title: "Комментарии",
                            field: "kommentariy",
                            minWidth: 300,
                            widthGrow: 2,
                            sorter: "string"
                        },
                    ],
                    controll_column_visiable: true,
                    apply_filters: true,
                });
            });
        </script>
    </x-slot:scripts>
</x-layout>
