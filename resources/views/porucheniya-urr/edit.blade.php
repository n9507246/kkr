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

                    <x-porucheniya-urr.forma-dobavleniya-porucheniya></x-porucheniya-urr.forma-dobavleniya-porucheniya>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection


