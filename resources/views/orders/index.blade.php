@extends('layouts.app')


@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-text me-2"></i>Список поручений</h2>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Новое поручение
        </a>
    </div>

    {{-- ФИЛЬТРЫ
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-filter"></i> Фильтры</h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Вх. номер</label>
                        <input type="text" class="form-control" name="incoming_number" placeholder="ВХ-..." value="{{ request('incoming_number') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Дата с</label>
                        <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Дата по</label>
                        <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Номер УРР</label>
                        <input type="text" class="form-control" name="urr_number" placeholder="12-..." value="{{ request('urr_number') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Статус ответа</label>
                        <select class="form-select" name="response_status">
                            <option value="">Все</option>
                            <option value="pending" {{ request('response_status') == 'pending' ? 'selected' : '' }}>Ожидает ответа</option>
                            <option value="sent" {{ request('response_status') == 'sent' ? 'selected' : '' }}>Ответ отправлен</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Применить фильтры
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="bi bi-eraser"></i> Сбросить
                        </a>
                    </div>
                </form>
            </div>
        </div>
    --}}

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
                        @forelse($orders ?? [] as $order)
                        <tr>
                            <td><a href="{{ route('orders.edit', $order['id'] ?? 1) }}" class="" title="Редактировать">
                                        {{ $order['incoming_number'] ?? '' }}1
                                    </a>
                                </td>
                            <td>{{ $order['incoming_date'] ?? '' }}2</td>
                            <td>{{ $order['urr_number'] ?? '' }}</td>
                            <td>{{ $order['urr_date'] ?? '' }}</td>
                            
                            <td>{{ $order['outgoing_number'] ?? '' }}</td>
                            <td>{{ $order['outgoing_date'] ?? '' }}</td>

                            <td>{{ $order['description'] ?? '' }}</td>
                            <td class="text-center">{{ $order['works_count'] ?? '' }}</td>

                            

                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('orders.show', $order['id'] ?? 1) }}" class="btn btn-info" title="Просмотр">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('orders.edit', $order['id'] ?? 1) }}" class="btn btn-warning" title="Редактировать">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" title="Удалить" onclick="confirmDelete({{ $order['id'] ?? 1 }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-4">
                                <i class="bi bi-inbox fs-1 d-block text-muted mb-2"></i>
                                <p class="text-muted mb-0">Нет распоряжений</p>
                                <a href="{{ route('orders.create') }}" class="btn btn-primary btn-sm mt-2">
                                    Создать первое распоряжение
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- ПАГИНАЦИЯ 
        @if(isset($orders) && method_exists($orders, 'links'))
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
        @endif--}}
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
                <p>Вы уверены, что хотите удалить это распоряжение?</p>
                <p class="text-danger"><small>Все связанные кадастровые номера будут также удалены!</small></p>
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
    form.action = `/orders/${id}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endsection