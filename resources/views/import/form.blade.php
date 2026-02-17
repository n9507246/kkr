@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>Импорт кадастровых номеров из Excel</h4>
                </div>

                <div class="card-body">
                    {{-- Информация о поручении --}}
                    <div class="alert alert-info">
                        <strong>Поручение:</strong> {{ $order->incoming_number }} 
                        от {{ $order->incoming_date }}
                    </div>

                    {{-- Инструкция --}}
                    <div class="mb-4">
                        <h5>Инструкция:</h5>
                        <ol>
                            <li>Скачайте шаблон Excel (кнопка ниже)</li>
                            <li>Заполните файл данными</li>
                            <li>Сохраните и загрузите файл</li>
                        </ol>
                    </div>

                    {{-- Форма загрузки --}}
                    <form action="{{ route('orders.import', $order->id) }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        
                        @csrf

                        <div class="mb-3">
                            <label for="file" class="form-label">Выберите Excel файл</label>
                            <input type="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   id="file" 
                                   name="file" 
                                   accept=".xlsx,.xls,.csv"
                                   required>
                            <small class="text-muted">
                                Поддерживаемые форматы: XLSX, XLS, CSV (макс. 10MB)
                            </small>
                            
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                Загрузить и импортировать
                            </button>
                            <div>
                                <a href="#" class="btn btn-success me-2">
                                    Скачать шаблон
                                </a>
                                <a href="/orders/{{ $order->id }}" class="btn btn-secondary">
                                    Отмена
                                </a>
                            </div>
                        </div>
                    </form>

                    {{-- Блок для ошибок импорта --}}
                    @if(session('error'))
                        <div class="alert alert-danger mt-3">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success mt-3">
                            {{ session('success') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection