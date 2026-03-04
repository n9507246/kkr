@props(['poruchenie' => null])
<div class="tab-pane fade show active" id="main" role="tabpanel">
    @php
        $poruchenieId = data_get($poruchenie, 'id');
        $vhodNomer = data_get($poruchenie, 'vhod_nomer', '');
        $vhodData = data_get($poruchenie, 'vhod_data', '');
        $urrNomer = data_get($poruchenie, 'urr_nomer', '');
        $urrData = data_get($poruchenie, 'urr_data', '');
        $opisanie = data_get($poruchenie, 'opisanie', '');
        $ishodNomer = data_get($poruchenie, 'ishod_nomer', '');
        $ishodData = data_get($poruchenie, 'ishod_data', '');
    @endphp
    <form method="POST" action="{{ isset($poruchenie) ? route('porucheniya-urr.obnovit-poruchenie', $poruchenieId ?? 1) : route('porucheniya-urr.sohranit-poruchenie') }}" id="mainForm">
        @csrf
        @if(isset($poruchenie))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">Ваши реквизиты</h5>

                <div class="mb-3">
                    <label for="vhod_nomer" class="form-label">Входящий номер <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('vhod_nomer') is-invalid @enderror"
                        id="vhod_nomer"
                        name="vhod_nomer"
                        value="{{ old('vhod_nomer', $vhodNomer) }}"
                        placeholder="ВХ-123/2025"
                        required>
                    @error('vhod_nomer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="vhod_data" class="form-label">Дата регистрации <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('vhod_data') is-invalid @enderror"
                        id="vhod_data"
                        name="vhod_data"
                        value="{{ old('vhod_data', $vhodData) }}"
                        required>
                    @error('vhod_data')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">Реквизиты письма УРР</h5>

                <div class="mb-3">
                    <label for="urr_nomer" class="form-label">Номер письма УРР <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('urr_nomer') is-invalid @enderror"
                        id="urr_nomer"
                        name="urr_nomer"
                        value="{{ old('urr_nomer', $urrNomer) }}"
                        placeholder="12-3456/25"
                        required>
                    @error('urr_nomer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="urr_data" class="form-label">Дата письма УРР <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('urr_data') is-invalid @enderror"
                        id="urr_data"
                        name="urr_data"
                        value="{{ old('urr_data', $urrData) }}"
                        required>
                    @error('urr_data')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="opisanie" class="form-label">Описание поручения</label>
                    <textarea class="form-control @error('opisanie') is-invalid @enderror"
                            id="opisanie"
                            name="opisanie"
                            rows="2"
                            placeholder="Краткое описание работ...">{{ old('opisanie', $opisanie) }}</textarea>
                    @error('opisanie')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ИСХОДЯЩЕЕ ПИСЬМО --}}
        <div class="row mt-2">
            <div class="col-12">
                <h5 class="border-bottom pb-2 mb-3">Исходящий ответ</h5>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ishod_nomer" class="form-label">Исходящий номер</label>
                    <input type="text"
                        class="form-control"
                        id="ishod_nomer"
                        name="ishod_nomer"
                        value="{{ old('ishod_nomer', $ishodNomer) }}"
                        placeholder="ИСХ-456/2025">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="ishod_data" class="form-label">Дата отправки</label>
                    <input type="date"
                        class="form-control"
                        id="ishod_data"
                        name="ishod_data"
                        value="{{ old('ishod_data', $ishodData) }}">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a class="btn btn-secondary" href="{{ route('porucheniya-urr.spisok-porucheniy')}}">
                <i class="bi bi-arrow-left"></i>
                Назад
            </a>
            <div class="d-flex gap-2">
                @if(isset($poruchenie) && $poruchenieId)
                    <button type="button"
                            class="btn btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#deletePoruchenieModal">
                        <i class="bi bi-trash"></i> Удалить поручение
                    </button>
                @endif
                <button type="submit" class="btn btn-primary" >
                    Сохранить
                </button>
            </div>
        </div>
    </form>

    @if(isset($poruchenie) && $poruchenieId)
        <div class="modal fade" id="deletePoruchenieModal" tabindex="-1" aria-labelledby="deletePoruchenieModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePoruchenieModalLabel">Подтверждение удаления</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
                    </div>
                    <div class="modal-body">
                        Вы действительно хотите удалить поручение
                        <strong>{{ $vhodNomer }}</strong>?
                        Это действие нельзя отменить.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                        <form method="POST" action="{{ route('porucheniya-urr.udalit-poruchenie', ['poruchenie_urr' => $poruchenieId]) }}">
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
    @endif
</div>
