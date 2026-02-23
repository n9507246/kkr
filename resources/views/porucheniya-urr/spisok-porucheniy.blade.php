@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text me-2"></i>Список поручений УРР</h2>
        <a href="{{ route('porucheniya-urr.sozdat-poruchenie') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Новое поручение
        </a>
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
    var table = new Tabulator("#report-table", {
        height: "600px",
        layout: "fitColumns",
        placeholder: "Нет данных",

        ajaxURL: "{{ route('porucheniya-urr.spisok-porucheniy') }}",

        // ВАЖНО: убираем remote pagination из таблицы
        // pagination: "remote",  // ЗАКОММЕНТИРУЙТЕ ЭТО

        // Просто получаем все данные сразу
        ajaxResponse: function(url, params, response) {
            console.log("Ответ сервера:", response);
            // Возвращаем ТОЛЬКО массив данных
            return response.data || [];
        },

        columns: [
            {
                title: "Вх. номер",
                field: "vhod_nomer",
                width: 120,
                formatter: function(cell) {
                    const data = cell.getData();
                    const url = `/porucheniya-urr/${data.id}/redaktirovat-poruchenie`;
                    return `<a href="${url}" class="text-decoration-none">${cell.getValue() || '-'}</a>`;
                }
            },
            {
                title: "Вх. дата",
                field: "vhod_data",
                width: 100,
                formatter: function(cell) {
                    return cell.getValue() ? new Date(cell.getValue()).toLocaleDateString('ru-RU') : '-';
                }
            },
            {
                title: "Номер УРР",
                field: "urr_nomer",
                width: 120
            },
            {
                title: "Дата УРР",
                field: "urr_data",
                width: 100,
                formatter: function(cell) {
                    return cell.getValue() ? new Date(cell.getValue()).toLocaleDateString('ru-RU') : '-';
                }
            },
            {
                title: "Исх. номер",
                field: "ishod_nomer",
                width: 120
            },
            {
                title: "Исх. дата",
                field: "ishod_data",
                width: 100,
                formatter: function(cell) {
                    return cell.getValue() ? new Date(cell.getValue()).toLocaleDateString('ru-RU') : '-';
                }
            },
            {
                title: "Описание",
                field: "opisanie",
                minWidth: 200
            },
            {
                title: "Действия",
                width: 100,
                hozAlign: "center",
                headerSort: false,
                formatter: function(cell) {
                    const data = cell.getData();
                    return `
                        <div class="btn-group btn-group-sm">
                            <a href="/porucheniya-urr/${data.id}/redaktirovat-poruchenie" class="btn btn-warning" title="Редактировать">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-danger" title="Удалить" onclick="confirmDelete(${data.id})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });
});
</script>
@endpush
@endsection
