@extends('layouts.app')

{{-- ТЕСТОВЫЙ МАССИВ --}}
@php
    $works = [
        [
            'id' => 1,
            'incoming_number' => 'ВХ-123/2025',
            'incoming_date' => '17.02.2025',
            'urr_number' => '12-3456/25',
            'urr_date' => '10.02.2025',
            'cadastral_number' => '50:09:0070504:123',
            'object_type' => 'Земельный участок',
            'work_type' => 'Отчет',
            'executor' => 'Иванов И.И.',
            'status' => 'completed',
            'completion_date' => '25.02.2025'
        ],
        [
            'id' => 2,
            'incoming_number' => 'ВХ-124/2025',
            'incoming_date' => '18.02.2025',
            'urr_number' => '12-3457/25',
            'urr_date' => '11.02.2025',
            'cadastral_number' => '50:09:0070504:124',
            'object_type' => 'Здание',
            'work_type' => 'Технический план',
            'executor' => 'Петров П.П.',
            'status' => 'in_progress',
            'completion_date' => null
        ],
        [
            'id' => 3,
            'incoming_number' => 'ВХ-125/2025',
            'incoming_date' => '19.02.2025',
            'urr_number' => '12-3458/25',
            'urr_date' => '12.02.2025',
            'cadastral_number' => '50:09:0070504:125',
            'object_type' => 'Сооружение',
            'work_type' => 'Акт обследования',
            'executor' => 'Сидорова А.А.',
            'status' => 'problem',
            'completion_date' => null
        ],
        [
            'id' => 4,
            'incoming_number' => 'ВХ-126/2025',
            'incoming_date' => '20.02.2025',
            'urr_number' => '12-3459/25',
            'urr_date' => '13.02.2025',
            'cadastral_number' => '50:09:0070504:126',
            'object_type' => 'Помещение',
            'work_type' => 'Заключение',
            'executor' => 'Иванов И.И.',
            'status' => 'completed',
            'completion_date' => '26.02.2025'
        ],
        [
            'id' => 5,
            'incoming_number' => 'ВХ-127/2025',
            'incoming_date' => '21.02.2025',
            'urr_number' => '12-3460/25',
            'urr_date' => '14.02.2025',
            'cadastral_number' => '50:09:0070504:127',
            'object_type' => 'Земельный участок',
            'work_type' => 'Межевой план',
            'executor' => 'Петров П.П.',
            'status' => 'checking',
            'completion_date' => null
        ],
    ];
@endphp

@section('content')
<div class="container-fluid px-4"> {{-- container-fluid на всю ширину --}}
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

            {{-- ТАБЛИЦА НА ВСЮ ШИРИНУ --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped w-100">
                    <thead class="table-primary">
                        <tr>
                            <th style="width: 3%">№</th>
                            <th style="width: 12%">Кадастровый номер</th>
                            <th style="width: 8%">Тип объекта</th>
                            <th style="width: 7%">Вх. номер</th>
                            <th style="width: 6%">Вх. дата</th>
                            <th style="width: 7%">Номер УРР</th>
                            <th style="width: 6%">Дата УРР</th>

                            <th style="width: 10%">Тип работ</th>
                            <th style="width: 10%">Исполнитель</th>
                            {{-- <th style="width: 8%">Статус</th> --}}
                            <th style="width: 8%">Дата заверш.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($works as $work)
                        <tr>
                            <td>{{ $work['id'] }}</td>
                            <td>{{ $work['cadastral_number'] }}</td>
                            <td>
                                @if($work['object_type'] == 'Земельный участок')
                                    ЗУ
                                @else
                                    {{ $work['object_type'] }}
                                @endif
                            </td>
                            
                            <td>{{ $work['incoming_number'] }}</td>
                            <td>{{ $work['incoming_date'] }}</td>
                            <td>{{ $work['urr_number'] }}</td>
                            <td>{{ $work['urr_date'] }}</td>

                            <td>{{ $work['work_type'] }}</td>
                            <td>{{ $work['executor'] }}</td>
                            <td>
                                @php
                                    $statusClass = match($work['status']) {
                                        'completed' => 'bg-success',
                                        'in_progress' => 'bg-primary',
                                        'problem' => 'bg-danger',
                                        'checking' => 'bg-warning',
                                        default => 'bg-secondary'
                                    };
                                    $statusText = match($work['status']) {
                                        'completed' => 'Выполнен',
                                        'in_progress' => 'В работе',
                                        'problem' => 'Проблема',
                                        'checking' => 'На проверке',
                                        default => $work['status']
                                    };
                                @endphp
                                {{-- <span class="badge {{ $statusClass }}">{{ $statusText }}</span> --}}
                            </td>
                            <td>{{ $work['completion_date'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- ПАГИНАЦИЯ (для примера) --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>Показано 5 из 5 записей</div>
                <nav>
                    <ul class="pagination">
                        <li class="page-item disabled"><a class="page-link" href="#">«</a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item disabled"><a class="page-link" href="#">»</a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection