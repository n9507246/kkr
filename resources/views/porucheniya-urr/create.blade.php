@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-plus-circle"></i> Новое поручение УРР
                    </h4>
                </div>

                <div class="card-body">

                        <x-porucheniya-urr.nav-tabs>
                            <x-porucheniya-urr.tab-item :active="true" icon="bi-file-text">
                                Основные данные
                            </x-porucheniya-urr.tab-item>
                        </x-porucheniya-urr.nav-tabs>

                    <x-porucheniya-urr.forma-dobavleniya-porucheniya></x-porucheniya-urr.forma-dobavleniya-porucheniya>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

