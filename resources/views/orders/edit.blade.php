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
                    <ul class="nav nav-tabs mb-4" >
                        <li class="nav-item" >
                            <a class="nav-link active" id="main-tab" type="button" >
                                <i class="bi bi-file-text"></i> Основные данные
                            </a>
                        </li>
                        <li class="nav-item" >
                            <a class="nav-link" id="cadastral-tab"   type="button" >
                                <i class="bi bi-grid"></i> Кадастровые номера
                                <span class="badge bg-secondary" id="itemsCount">0</span>
                            </a>
                        </li>
                        <li class="nav-item" >
                            <a class="nav-link" id="import-tab"  data-bs-target="#import" type="button" >
                                <i class="bi bi-file-excel"></i> Импорт из Excel
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content" id="orderTabsContent">    
                        <x-order.add-order-form></x-order.add-order-form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
