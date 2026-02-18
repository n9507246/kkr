@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text me-2"></i>Список поручений УРР</h2>
        <a href="{{ route('porucheniya-urr.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Новое поручение
        </a>
    </div>

    {{-- ТАБЛИЦА С поручениями --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>Вх. номер</th>
                            <th>Вх. дата</th>
                            <th>Номер УРР</th>
                            <th>Дата УРР</th>
                            <th>Исх. номер</th>
                            <th>Исх. дата</th>
                            <th>Описание</th>
                            <th>Кол-во работ</th>
                            <th style="width: 120px">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($spisok_porucheniy ?? [] as $poruchenie)
                        <tr>
                            <td>
                                <a href="{{ route('porucheniya-urr.edit', $poruchenie->id ?? 1) }}" title="Редактировать">
                                    {{ $poruchenie->incoming_number ?? 'Б/Н' }}
                                </a>
                            </td>
                            <td>{{ $poruchenie->incoming_date ?? '' }}</td>
                            <td>{{ $poruchenie->urr_number ?? '' }}</td>
                            <td>{{ $poruchenie->urr_date ??  '' }}</td>
                            <td>{{ $poruchenie->outgoing_number ?? '' }}</td>
                            <td>{{ $poruchenie->outgoing_date ??  '' }}</td>
                            <td>{{ $poruchenie->description ?? '' }}</td>
                            <td class="text-center">{{ $poruchenie->works_count ?? 0 }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('porucheniya-urr.show', $poruchenie->id ?? 1) }}" class="btn btn-info" title="Просмотр">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('porucheniya-urr.edit', $poruchenie->id ?? 1) }}" class="btn btn-warning" title="Редактировать">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Удалить" onclick="confirmDelete({{ $poruchenie->id ?? 1 }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 d-block text-muted mb-2"></i>
                                <p class="text-muted mb-0">Нет поручений</p>
                                <a href="{{ route('porucheniya-urr.create') }}" class="btn btn-primary btn-sm mt-2">
                                    Создать первое поручение
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ПАГИНАЦИЯ --}}
        @if(isset($porucheniya) && method_exists($porucheniya, 'links'))
        <div class="card-footer">
            {{ $porucheniya->links() }}
        </div>
        @endif
    </div>
</div>

{{-- МОДАЛЬНОЕ ОКНО ПОДТВЕРЖДЕНИЯ УДАЛЕНИЯ --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Вы уверены, что хотите удалить это поручение?</p>
                <p class="text-danger"><small>Все связанные данные будут также удалены!</small></p>
            </div>
            <div class="modal-footer">
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger">Удалить</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    const form = document.getElementById('deleteForm');
    form.action = '{{ url("/porucheniya-urr") }}/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection
