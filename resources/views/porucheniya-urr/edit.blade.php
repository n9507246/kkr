@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        @if(isset($order))
                            <i class="bi bi-pencil"></i> Редактирование распоряжения
                        @else
                            <i class="bi bi-plus-circle"></i> Новое распоряжение УРР
                        @endif
                    </h4>
                </div>

                <div class="card-body">
                    <!-- Табы -->
                    <ul class="nav nav-tabs mb-4" id="orderTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="main-tab" data-bs-toggle="tab" data-bs-target="#main" type="button" role="tab" aria-controls="main" aria-selected="true">
                                <i class="bi bi-file-text"></i> Основные данные
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cadastral-tab" data-bs-toggle="tab" data-bs-target="#cadastral" type="button" role="tab" aria-controls="cadastral" aria-selected="false">
                                <i class="bi bi-grid"></i> Кадастровые номера
                                <span class="badge bg-secondary" id="itemsCount">0</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="import-tab" data-bs-toggle="tab" data-bs-target="#import" type="button" role="tab" aria-controls="import" aria-selected="false">
                                <i class="bi bi-file-excel"></i> Импорт из Excel
                            </button>
                        </li>
                    </ul>

                    <!-- Контент табов -->
                    <div class="tab-content" id="orderTabsContent">
                        <!-- Вкладка "Основные данные" -->
                        <div class="tab-pane fade show active" id="main" role="tabpanel" aria-labelledby="main-tab">
                            <x-porucheniya-urr.forma-dobavleniya-porucheniya></x-order.forma-dobavleniya-porucheniya>
                        </div>

                        <!-- Вкладка "Кадастровые номера" -->
                        <div class="tab-pane fade" id="cadastral" role="tabpanel" aria-labelledby="cadastral-tab">
                            <x-porucheniya-urr.forma-dobavleniya-obekta></x-order.forma-dobavleniya-obekta>
                        </div>

                        <!-- Вкладка "Импорт из Excel" -->
                        <div class="tab-pane fade" id="import" role="tabpanel" aria-labelledby="import-tab">
                            <x-porucheniya-urr.forma-importa-obektov-exel></x-order.forma-importa-obektov-exel>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Используем встроенную функциональность Bootstrap 5
    // Ничего не нужно делать дополнительно, если Bootstrap загружен правильно

    // Если вы хотите, чтобы при переходе на страницу кадастровых номеров открывалась соответствующая вкладка
    @if(isset($poruchenie) && request()->routeIs('porucheniya-urr.obekty-nedvizhimosti.create'))
        // Активируем вкладку с кадастровыми номерами
        var triggerTabList = [].slice.call(document.querySelectorAll('#cadastral-tab'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)
            tabTrigger.show()
        })
    @endif
});
</script>
@endpush

@stack('scripts')
