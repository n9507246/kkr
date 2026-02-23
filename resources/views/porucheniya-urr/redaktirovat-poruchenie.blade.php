@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil"></i> Редактирование поручения
                        </h4>
                        <div class="text-end">
                            <span class="badge bg-light text-primary me-2" title="Дата регистрации">
                                <i class="bi bi-calendar3"></i> Вх: {{ $vhod_data }}
                            </span>
                            <span class="badge bg-light text-primary" title="Дата письма УРР">
                                <i class="bi bi-envelope"></i> УРР: {{ $urr_data }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">

                    <x-porucheniya-urr.nav-tabs>
                        <x-porucheniya-urr.tab-item
                            :url="route('porucheniya-urr.sozdat-poruchenie', ['poruchenie_urr' => $poruchenie->id])"
                            :active="request()->routeIs('porucheniya-urr.redaktirovat-poruchenie')"
                            icon="bi-file-text"
                        >

                            Основные данные
                        </x-porucheniya-urr.tab-item>

                        <x-porucheniya-urr.tab-item
                            :url="route('porucheniya-urr.obekti-nedvizhimosti.spisok-obektov', ['poruchenie_urr' => $poruchenie->id])"
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

                    <x-porucheniya-urr.forma-dobavleniya-porucheniya :poruchenie="$poruchenie"></x-porucheniya-urr.forma-dobavleniya-porucheniya>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
