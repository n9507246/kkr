@extends('layouts.app')
<!-- @php dump($obekt) @endphp -->
@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil-square"></i> Редактирование кадастрового объекта
                    </h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('obekti-nedvizhimosti.obnovit-obekt', $obekt->id) }}" id="editForm">
                        @csrf

                        <!-- Информация о поручении (только для чтения) -->
                        <div class="alert alert-info">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle fs-4 me-2"></i>
                                <div>
                                    <strong>Поручение УРР:</strong>
                                    {{ $obekt->poruchenie->vhod_nomer ?? 'Не указано' }}
                                    от {{ $obekt->poruchenie->vhod_data ? \Carbon\Carbon::parse($obekt->poruchenie->vhod_data)->format('d.m.Y') : 'неизвестно' }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Левая колонка -->
                            <div class="">
                                <!-- Кадастровый номер -->
                                    <div class="mb-3">
                                        <label for="kadastroviy_nomer" class="form-label">
                                            Кадастровый номер <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                            class="form-control @error('kadastroviy_nomer') is-invalid @enderror"
                                            id="kadastroviy_nomer"
                                            name="kadastroviy_nomer"
                                            value="{{ old('kadastroviy_nomer', $obekt->kadastroviy_nomer) }}"
                                            placeholder="50:09:0070504:123"
                                            required>
                                        @error('kadastroviy_nomer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <!-- Тип объекта -->
                                    <div class="mb-3">
                                        <label for="tip_obekta_nedvizhimosti" class="form-label">Тип объекта</label>
                                        <select class="form-select @error('tip_obekta_id') is-invalid @enderror"
                                                id="tip_obekta_id"
                                                name="tip_obekta_id">
                                            <option value="">Выберите тип...</option>
                                            @foreach($tipyObektov as $tip)
                                                <option value="{{ $tip->id }}" {{ old('tip_obekta_id', $obekt->tip_obekta_id) == $tip->id ? 'selected' : '' }}>{{ $tip->nazvanie }} ({{ $tip->abbreviatura }})</option>
                                            @endforeach
                                        </select>
                                        @error('tip_obekta_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <!-- Вид работ -->
                                    <div class="mb-3">
                                        <label for="vid_rabot" class="form-label">Вид работ</label>
                                        <select class="form-select @error('vid_rabot_id') is-invalid @enderror"
                                                id="vid_rabot_id"
                                                name="vid_rabot_id">
                                            <option value="">Выберите вид работ...</option>
                                            @foreach($vidiRabot as $vid)
                                                <option value="{{ $vid->id }}" {{ old('vid_rabot_id', $obekt->vid_rabot_id) == $vid->id ? 'selected' : '' }}>{{ $vid->nazvanie }}</option>
                                            @endforeach
                                        </select>

                                        @error('vid_rabot_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <!-- Дата завершения -->
                                    <div class="mb-3">
                                        <label for="data_zaversheniya" class="form-label">Дата завершения</label>
                                        <input type="date"
                                            class="form-control @error('data_zaversheniya') is-invalid @enderror"
                                            id="data_zaversheniya"
                                            name="data_zaversheniya"
                                            value="{{ old('data_zaversheniya', $obekt->data_zaversheniya ? \Carbon\Carbon::parse($obekt->data_zaversheniya)->format('Y-m-d') : '') }}">
                                        @error('data_zaversheniya')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                <!-- Комментарий -->
                                    <div class="mb-3">
                                        <label for="kommentariy" class="form-label">Комментарий</label>
                                        <textarea class="form-control @error('kommentariy') is-invalid @enderror"
                                                    id="kommentariy"
                                                    name="kommentariy"
                                                    rows="4"
                                                    placeholder="Дополнительная информация...">{{ old('kommentariy', $obekt->kommentariy) }}</textarea>
                                        @error('kommentariy')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                        </div>

                        <!-- Скрытое поле ID поручения -->
                        <input type="hidden" name="id_porucheniya_urr" value="{{ $obekt->poruchenie_id }}">

                        <hr class="my-4">

                        <!-- Кнопки -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('obekti-nedvizhimosti.spisok-obektov', $obekt->poruchenie_id) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>

                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary" id="saveBtn">
                                    <i class="bi bi-save"></i> Сохранить изменения
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Функция сброса формы
function resetForm() {
    if (confirm('Сбросить все изменения?')) {
        document.getElementById('editForm').reset();
    }
}

// Автоматическое форматирование кадастрового номера
// document.getElementById('kadastroviy_nomer').addEventListener('input', function(e) {
//     // Простая маска, можно усложнить при необходимости
//     let value = this.value.replace(/[^\d:]/g, '');
//     this.value = value;
// });

// // Валидация перед отправкой
// document.getElementById('editForm').addEventListener('submit', function(e) {
//     let kadastr = document.getElementById('kadastroviy_nomer').value;
//     if (kadastr && !kadastr.match(/^\d{2}:\d{2}:\d{6,7}:\d{1,5}$/)) {
//         if (!confirm('Кадастровый номер имеет нестандартный формат. Продолжить?')) {
//             e.preventDefault();
//         }
//     }
// });
</script>
@endpush
@endsection
