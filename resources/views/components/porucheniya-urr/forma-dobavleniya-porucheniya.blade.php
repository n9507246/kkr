@props(['poruchenie' => null])
<div class="tab-pane fade show active" id="main" role="tabpanel">
    <form method="POST" action="{{ isset($poruchenie) ? route('porucheniya-urr.obnovit-poruchenie', $poruchenie['id'] ?? 1) : route('porucheniya-urr.sohranit-poruchenie') }}" id="mainForm">
        @csrf
        @if(isset($poruchenie))
            @method('PUT')
        @endif

        <div class="row">
            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">Ваши реквизиты</h5>

                <div class="mb-3">
                    <label for="incoming_number" class="form-label">Входящий номер <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('incoming_number') is-invalid @enderror"
                        id="incoming_number"
                        name="incoming_number"
                        value="{{ old('incoming_number', $poruchenie['incoming_number'] ?? '') }}"
                        placeholder="ВХ-123/2025"
                        required>
                    @error('incoming_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="incoming_date" class="form-label">Дата регистрации <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('incoming_date') is-invalid @enderror"
                        id="incoming_date"
                        name="incoming_date"
                        value="{{ old('incoming_date', $poruchenie['incoming_date'] ?? '') }}"
                        required>
                    @error('incoming_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                <h5 class="border-bottom pb-2 mb-3">Реквизиты письма УРР</h5>

                <div class="mb-3">
                    <label for="urr_number" class="form-label">Номер письма УРР <span class="text-danger">*</span></label>
                    <input type="text"
                        class="form-control @error('urr_number') is-invalid @enderror"
                        id="urr_number"
                        name="urr_number"
                        value="{{ old('urr_number', $poruchenie['urr_number'] ?? '') }}"
                        placeholder="12-3456/25"
                        required>
                    @error('urr_number')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="urr_date" class="form-label">Дата письма УРР <span class="text-danger">*</span></label>
                    <input type="date"
                        class="form-control @error('urr_date') is-invalid @enderror"
                        id="urr_date"
                        name="urr_date"
                        value="{{ old('urr_date', $poruchenie['urr_date'] ?? '') }}"
                        required>
                    @error('urr_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="mb-3">
                    <label for="description" class="form-label">Описание поручения</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                            id="description"
                            name="description"
                            rows="2"
                            placeholder="Краткое описание работ...">{{ old('description', $poruchenie['description'] ?? '') }}</textarea>
                    @error('description')
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
                    <label for="outgoing_number" class="form-label">Исходящий номер</label>
                    <input type="text"
                        class="form-control"
                        id="outgoing_number"
                        name="outgoing_number"
                        value="{{ old('outgoing_number', $poruchenie['outgoing_number'] ?? '') }}"
                        placeholder="ИСХ-456/2025">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="outgoing_date" class="form-label">Дата отправки</label>
                    <input type="date"
                        class="form-control"
                        id="outgoing_date"
                        name="outgoing_date"
                        value="{{ old('outgoing_date', $poruchenie['outgoing_date'] ?? '') }}">
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a class="btn btn-secondary" href="{{ route('porucheniya-urr.spisok-porucheniy')}}">
                Выйти без сохранения
            </a>
            <button type="submit" class="btn btn-primary" >
                Сохранить
            </button>
        </div>
    </form>
</div>
