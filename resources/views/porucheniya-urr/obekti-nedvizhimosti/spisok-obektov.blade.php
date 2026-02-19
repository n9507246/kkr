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
                            :url="route('porucheniya-urr.redaktirovat-poruchenie', ['poruchenie_urr' => $id_poruchenie])"
                            :active="request()->routeIs('porucheniya-urr.redaktirovat-poruchenie')"
                            icon="bi-file-text"
                        >   

                            Основные данные
                        </x-porucheniya-urr.tab-item>

                        <x-porucheniya-urr.tab-item
                            :url="route('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', ['poruchenie_urr' => $id_poruchenie])"
                            :active="request()->routeIs('porucheniya-urr.obekti-nedvizhimosti.*')"
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
                    <form method="POST" action="{{ route('porucheniya-urr.obekti-nedvizhimosti.sozdat-obekt', ['poruchenie_urr' => $id_poruchenie]) }}">
                        @csrf
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="kadastroviy_nomer" class="form-label">Кадастровый номер</label>
                                <input type="text" class="form-control" id="kadastroviy_nomer" name="kadastroviy_nomer">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vid_rabot" class="form-label">Тип</label>
                                <select class="form-control" id="kadastroviy_nomer" name="vid_rabot">
                                    <option value="ЗУ">ЗУ</option>
                                    <option value="ОКС">ОКС</option>
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
                            @foreach($spisok_obektov ?? [] as $item)
                            <tr>
                                <td>{{ $item->kadastroviy_nomer }}</td>
                                <td>{{ $item->tip_obekta_nedvizhimosti }}</td>
                                <td>     
                                    <form method="POST" 
                                        action="{{ route('porucheniya-urr.obekti-nedvizhimosti.udalit-obekt', [
                                            'poruchenie_urr' => $id_poruchenie,
                                            'obekt' => $item->id
                                        ]) }}"
                                        onsubmit="return confirm('Вы уверены?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i> Удалить
                                        </button>
                                    </form>
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
