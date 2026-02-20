@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Редактирование поручения
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

                    <!-- ФОРМА СОЗДАНИЯ ОБЪЕКТА -->
                        <form method="POST" action="{{ route('porucheniya-urr.obekti-nedvizhimosti.sozdat-obekt', ['poruchenie_urr' => $id_poruchenie]) }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kadastroviy_nomer" class="form-label">Кадастровый номер <span class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('kadastroviy_nomer') is-invalid @enderror"
                                        id="kadastroviy_nomer"
                                        name="kadastroviy_nomer"
                                        value="{{ old('kadastroviy_nomer') }}"
                                        placeholder="Введите кадастровый номер">
                                    @error('kadastroviy_nomer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tip_obekta_nedvizhimosti" class="form-label">Тип <span class="text-danger">*</span></label>
                                    <select class="form-select @error('tip_obekta_nedvizhimosti') is-invalid @enderror"
                                            id="tip_obekta_nedvizhimosti"
                                            name="tip_obekta_nedvizhimosti">
                                        <option value="">Выберите тип...</option>
                                        <option value="ЗУ" {{ old('tip_obekta_nedvizhimosti') == 'ЗУ' ? 'selected' : '' }}>ЗУ</option>
                                        <option value="ОКС" {{ old('tip_obekta_nedvizhimosti') == 'ОКС' ? 'selected' : '' }}>ОКС</option>
                                    </select>
                                    @error('tip_obekta_nedvizhimosti')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i> Добавить объект
                            </button>
                        </form>

                    <hr class="my-4">

                    <h5>Список объектов</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Кадастровый номер</th>
                                    <th>Тип</th>
                                    <th>Дата добавления</th>
                                    <th>Действия</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($spisok_obektov ?? [] as $item)
                                <tr>
                                    <td>{{ $item->kadastroviy_nomer }}</td>
                                    <td>
                                        <span class="">
                                            {{ $item->tip_obekta_nedvizhimosti }}
                                        </span>
                                    </td>
                                    <td>{{ $item->created_at ? $item->created_at->format('d.m.Y') : '-' }}</td>
                                    <td>
                                        <form method="POST"
                                            action="{{ route('porucheniya-urr.obekti-nedvizhimosti.udalit-obekt', [
                                                'poruchenie_urr' => $id_poruchenie,
                                                'obekt' => $item->id
                                            ]) }}"
                                            onsubmit="return confirm('Вы уверены, что хотите удалить объект?')">
                                            @csrf

                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                        Нет добавленных объектов
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>

</script>
@endpush
