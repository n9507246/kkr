<x-layout>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Список поручений УРР</h2>

    </div>

    @php $id_table = 'porucheniya-urr-table'; @endphp

    <x-smart-table.component :id="$id_table">
        <x-slot:control-panel>
            <div class='d-flex gap-2 mb-2'>
                <x-smart-table.column-controller>
                    <x-slot:btn-controll>
                        <x-smart-table.column-controller-btn/>
                    </x-slot:btn-controll>

                    <x-slot:dropdown-menu>
                        <x-smart-table.column-controller-dropdown :id="$id_table" style='min-width: 220px !important'/>
                    </x-slot:dropdown-menu>
                </x-smart-table.column-controller>

                <x-smart-table.filter-panel-btn :id="$id_table"/>
                
                <a href="{{ route('porucheniya-urr.sozdat-poruchenie') }}" class="btn btn-primary btn-sm ms-auto">
                    <i class="bi bi-plus-circle"></i> Новое поручение
                </a>
            </div>

            <x-smart-table.filter-panel :id="$id_table">
                <x-slot:filters>
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
                </x-slot:filters>
            </x-smart-table.filter-panel>
        </x-slot:control-panel>
    </x-smart-table.component>

@push('scripts')
    <script type="module">
        import { create_smart_table } from '{{ Vite::asset('resources/js/app.js') }}';

        document.addEventListener('DOMContentLoaded', function() {
            create_smart_table({
                id: '{{ $id_table }}',
                height: '80vh',
                ajaxURL: "{{ route('porucheniya-urr.spisok-porucheniy') }}",

                columns: [
                    {
                        title: "Вх. номер",
                        field: "vhod_nomer",
                        minWidth: 140,
                        frozen: true,
                        sorter: "string",
                        formatter: function(cell) {
                            const data = cell.getData();
                            return `<a href="/porucheniya-urr/${data.id}/redaktirovat-poruchenie" class="text-primary fw-bold text-decoration-none">${cell.getValue() || '-'}</a>`;
                        }
                    },
                    {
                        title: "Вх. дата",
                        field: "vhod_data",
                        minWidth: 120,
                        sorter: "date",
                        sorterParams: { format: "YYYY-MM-DD" },
                        formatter: (cell) => {
                            const val = cell.getValue();
                            return val ? new Date(val).toLocaleDateString('ru-RU') : '-';
                        }
                    },
                    {
                        title: "Номер УРР",
                        field: "urr_nomer",
                        minWidth: 140,
                        sorter: "string",
                        formatter: (cell) => cell.getValue() || '-'
                    },
                    {
                        title: "Дата УРР",
                        field: "urr_data",
                        minWidth: 120,
                        sorter: "date",
                        sorterParams: { format: "YYYY-MM-DD" },
                        formatter: (cell) => {
                            const val = cell.getValue();
                            return val ? new Date(val).toLocaleDateString('ru-RU') : '-';
                        }
                    },
                    {
                        title: "Исх. номер",
                        field: "ishod_nomer",
                        minWidth: 140,
                        sorter: "string",
                        formatter: (cell) => cell.getValue() || '-'
                    },
                    {
                        title: "Исх. дата",
                        field: "ishod_data",
                        minWidth: 120,
                        sorter: "date",
                        sorterParams: { format: "YYYY-MM-DD" },
                        formatter: (cell) => {
                            const val = cell.getValue();
                            return val ? new Date(val).toLocaleDateString('ru-RU') : '-';
                        }
                    },
                    {
                        title: "Описание",
                        field: "opisanie",
                        minWidth: 280,
                        widthGrow: 2,
                        sorter: "string",
                        formatter: (cell) => cell.getValue() || '-'
                    },
                ],
                controll_column_visiable: true,
                apply_filters: true,
            });
        });
    </script>
@endpush

</x-layout>
