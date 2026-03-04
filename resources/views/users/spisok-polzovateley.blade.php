<x-layout>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0">Список пользователей</h2>
    </div>

    @php $id_table = 'users-table'; @endphp

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
                <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm ms-auto">
                    <i class="bi bi-plus-circle"></i> Новый пользователь
                </a>
            </div>

            <x-smart-table.filter-panel :id="$id_table">
                <x-slot:filters>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-bold">Имя</label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="Введите имя...">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small text-muted fw-bold">Email</label>
                        <input type="text" name="email" class="form-control form-control-sm" placeholder="Введите email...">
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
                ajaxURL: "{{ route('users.index') }}",

                columns: [
                    {
                        title: "Имя",
                        field: "name",
                        minWidth: 180,
                        sorter: "string",
                        formatter: (cell) => {
                            const d = cell.getData();
                            return `<a href="/users/${d.id}/redaktirovat-polzovatelya" class="text-primary fw-bold text-decoration-none">${cell.getValue() || '-'}</a>`;
                        }
                    },
                    {
                        title: "Email",
                        field: "email",
                        minWidth: 260,
                        sorter: "string",
                    },
                    {
                        title: "Создан",
                        field: "created_at",
                        minWidth: 140,
                        sorter: "date",
                        formatter: (cell) => {
                            const val = cell.getValue();
                            return val ? new Date(val).toLocaleDateString('ru-RU') : '-';
                        }
                    },
                ],
                controll_column_visiable: true,
                apply_filters: true,
            });
        });
    </script>
@endpush

</x-layout>
