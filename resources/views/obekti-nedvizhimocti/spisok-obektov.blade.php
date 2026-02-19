@extends('layouts.app')


<style>
    .table-responsive {
        position: relative;
        overflow-x: auto;
        white-space: nowrap;
    }
    
    .table-responsive table {
        border-collapse: separate;
        border-spacing: 0;
    }
    
    .table-responsive th:last-child,
    .table-responsive td:last-child {
        position: sticky;
        right: 0;
        background-color: white;
        box-shadow: -2px 0 5px rgba(0,0,0,0.1);
        z-index: 10;
    }
    
    .table-responsive th:last-child {
        background-color: #0d6efd;
        color: white;
    }
    
    .table-responsive td:last-child {
        background-color: #f8f9fa;
    }
    
    .table-responsive tr:hover td:last-child {
        background-color: #e9ecef;
    }
</style>


@section('content')
<div class="container-fluid px-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Отчет о выполненных работах</h2>

            {{-- ФИЛЬТРЫ --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Фильтры</h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Вх. номер</label>
                            <input type="text" class="form-control" name="incoming_number" placeholder="ВХ-...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Кадастровый номер</label>
                            <input type="text" class="form-control" name="cadastral_number" placeholder="50:09:...">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Тип объекта</label>
                            <select class="form-select" name="object_type">
                                <option value="">Все</option>
                                <option value="ЗУ">ЗУ</option>
                                <option value="Здание">Здание</option>
                                <option value="Сооружение">Сооружение</option>
                                <option value="Помещение">Помещение</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Статус</label>
                            <select class="form-select" name="status">
                                <option value="">Все</option>
                                <option value="completed">Выполнен</option>
                                <option value="in_progress">В работе</option>
                                <option value="problem">Проблема</option>
                                <option value="checking">На проверке</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Исполнитель</label>
                            <select class="form-select" name="executor">
                                <option value="">Все</option>
                                <option value="Иванов">Иванов</option>
                                <option value="Петров">Петров</option>
                                <option value="Сидорова">Сидорова</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">Применить фильтры</button>
                            <a href="{{ url()->current() }}" class="btn btn-secondary">Сбросить</a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ТАБЛИЦА С ЗАКРЕПЛЕННЫМ СТОЛБЦОМ --}}
            <div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch; max-height: 600px;">
                <table class="table table-bordered table-hover table-striped w-100" style="min-width: 1400px;">
                    <thead class="table-primary" style="position: sticky; top: 0; z-index: 20;">
                        <tr>
                            <th style="">Кадастровый номер</th>
                            <th style="">Тип объекта</th>
                            <th style="">Вх. номер</th>
                            <th style="">Вх. дата</th>
                            <th style="">Номер УРР</th>
                            <th style="">Дата УРР</th>
                            <th style="">Тип работ</th>
                            <th style="">Исполнитель</th>
                            <th style="">Дата заверш.</th>
                            <th style="width: 8%; position: sticky; right: 0; background-color: #0d6efd; color: white; box-shadow: -2px 0 5px rgba(0,0,0,0.2); z-index: 30;">Действия</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($spisok_obektov as $index => $obekt)
                        <tr>
                            <td>{{ $obekt['kadastroviy_nomer'] }}</td>
                            <td>{{ $obekt['tip_obekta_nedvizhimosti']}}</td>
                            <td>{{ $obekt?->poruchenie?->incoming_number }}</td>
                            <td>{{ $obekt?->poruchenie?->incoming_date }}</td>
                            <td>{{ $obekt?->poruchenie?->urr_number }}</td>
                            <td>{{ $obekt?->poruchenie?->urr_date }}</td>
                            <td>{{ $obekt['work_type'] }}</td>
                            <td>{{ $obekt['executor'] }}</td>
                            <td>{{ $obekt['completion_date'] ?? '-' }}</td>
                            <td style="position: sticky; right: 0; background-color: #f8f9fa; box-shadow: -2px 0 5px rgba(0,0,0,0.1); z-index: 15;">    
                                <div class="d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </button> 
                                    <button type="button" class="btn btn-sm btn-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- ПАГИНАЦИЯ --}}
            @if($spisok_obektov instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>Показано {{ $spisok_obektov->firstItem() }} - {{ $spisok_obektov->lastItem() }} из {{ $spisok_obektov->total() }} записей</div>
                    <nav>
                        {{ $spisok_obektov->links() }}
                    </nav>
                </div>
            @else
                <div class="mt-3">
                    Всего записей: {{ $spisok_obektov->count() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection