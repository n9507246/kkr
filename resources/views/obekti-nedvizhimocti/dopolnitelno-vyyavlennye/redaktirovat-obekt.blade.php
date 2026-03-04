@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square"></i> Редактирование доп. выявленного объекта
                        </h4>
                        <div class="text-end">
                            <span class="badge bg-white text-success me-2" title="Дата регистрации">
                                <i class="bi bi-calendar3"></i> {{ $obekt->poruchenie->vhod_data }}
                            </span>
                            <span class="badge bg-white text-success me-2" title="Дата письма УРР">
                                <i class="bi bi-envelope"></i> УРР: {{ $obekt->poruchenie->urr_data }}
                            </span>
                            <span class="badge bg-white text-success" title="Родительский объект">
                                <i class="bi bi-diagram-2"></i> Родитель: {{ $obekt->roditelskiyObekt->kadastroviy_nomer ?? '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('obekti-nedvizhimosti.obnovit-obekt', ['id_obekta' => $obekt->id]) }}" id="editDopolnitelniyForm">
                        @csrf

                        <div class="row">
                            <div class="">
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

                                <div class="mb-3">
                                    <label for="tip_obekta_id" class="form-label">Тип объекта</label>
                                    <select class="form-select @error('tip_obekta_id') is-invalid @enderror"
                                            id="tip_obekta_id"
                                            name="tip_obekta_id">
                                        <option value="">Выберите тип...</option>
                                        @foreach($tipyObektov as $tip)
                                            <option value="{{ $tip->id }}" {{ old('tip_obekta_id', $obekt->tip_obekta_id) == $tip->id ? 'selected' : '' }}>
                                                {{ $tip->nazvanie }} ({{ $tip->abbreviatura }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('tip_obekta_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="vid_rabot_id" class="form-label">Вид работ</label>
                                    <select class="form-select @error('vid_rabot_id') is-invalid @enderror"
                                            id="vid_rabot_id"
                                            name="vid_rabot_id">
                                        <option value="">Выберите вид работ...</option>
                                        @foreach($vidiRabot as $vid)
                                            <option value="{{ $vid->id }}" {{ old('vid_rabot_id', $obekt->vid_rabot_id) == $vid->id ? 'selected' : '' }}>
                                                {{ $vid->nazvanie }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('vid_rabot_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

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

                                <input type="hidden" name="id_porucheniya_urr" value="{{ $obekt->poruchenie_id }}">
                                <input type="hidden" name="roditelskiy_obekt_id" value="{{ $obekt->roditelskiy_obekt_id }}">
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('obekti-nedvizhimosti.redactirovat-obekt', ['id_obekta' => $obekt->roditelskiy_obekt_id]) }}" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Назад
                            </a>
                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteDopolnitelniyObektModal">
                                    <i class="bi bi-trash"></i> Удалить объект
                                </button>
                                <button type="submit" class="btn btn-success">
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

<div class="modal fade" id="deleteDopolnitelniyObektModal" tabindex="-1" aria-labelledby="deleteDopolnitelniyObektModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteDopolnitelniyObektModalLabel">Подтверждение удаления</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                Вы действительно хотите удалить доп. выявленный объект
                <strong>{{ $obekt->kadastroviy_nomer }}</strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <form method="POST" action="{{ route('obekty-nedvizhimosti.dopolnitelno-vyyavlennye.udalit-obekt', ['id_obekta' => $obekt->roditelskiy_obekt_id, 'id_dopolnitelnogo_obekta' => $obekt->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash"></i> Удалить
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
