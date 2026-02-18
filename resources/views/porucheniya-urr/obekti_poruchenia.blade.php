@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Редактирование поручение
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Табы -->
                    <x-porucheniya-urr.nav-tabs>
                        <x-porucheniya-urr.tab-item
                            :route="route('porucheniya-urr.edit', ['poruchenie_urr' => $id_poruchenie])"
                            :active="request()->routeIs('porucheniya-urr.edit')"
                            icon="bi-file-text"
                        >
                            Основные данные
                        </x-porucheniya-urr.tab-item>

                        <x-porucheniya-urr.tab-item
                            :route="route('porucheniya-urr.nedvizhimosti.create', ['poruchenie_urr' => $id_poruchenie])"
                            :active="request()->routeIs('porucheniya-urr.nedvizhimosti.*')"
                            icon="bi-grid"
                        >
                            Объекты
                        </x-porucheniya-urr.tab-item>

                        <x-porucheniya-urr.tab-item
                            icon="bi-file-excel"
                            :disabled="true"
                        >
                            Импорт из Excel
                        </x-porucheniya-urr.tab-item>
                    </x-porucheniya-urr.nav-tabs>

                    <!-- ФОРМА СОЗДАНИЯ ПОРУЧЕНИЯ -->
                    <form method="POST" action="{{ route('porucheniya-urr.store') }}">
                        @csrf
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="cadastral_number" class="form-label">Кадастровый номер</label>
                                <input type="text" class="form-control" id="cadastral_number" name="cadastral_number">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Тип</label>
                                <select class="form-control" id="type" name="type">
                                    <option value="ZU">ЗУ</option>
                                    <option value="OKS">ОКС</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Сохранить поручение</button>
                    </form>

                    <hr class="my-4">

                    <h5>Список поручений</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>

                                <th>Кадастровый номер</th>
                                <th>Тип</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($porucheniya ?? [] as $item)
                            <tr>
                                <td>{{ $item->cadastral_number }}</td>
                                <td>{{ $item->type }}</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-primary">Редактировать</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
