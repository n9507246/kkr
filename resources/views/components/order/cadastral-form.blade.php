@props(['order' => null])

<div class="mb-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Список кадастровых номеров</h5>
        <button type="button" class="btn btn-success btn-sm" id="addCadastralRow">
            <i class="bi bi-plus-circle"></i> Добавить строку
        </button>
    </div>

    <form id="cadastralForm">
        @csrf
        <div class="table-responsive">
            <table class="table table-bordered" id="cadastralTable">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%">#</th>
                        <th style="width: 35%">Кадастровый номер</th>
                        <th style="width: 20%">Тип объекта</th>
                        <th style="width: 20%">Тип работ</th>
                        <th style="width: 15%">Исполнитель</th>
                        <th style="width: 5%"></th>
                    </tr>
                </thead>
                <tbody id="cadastralRows">
                    {{-- Строки будут добавляться через JavaScript --}}
                </tbody>
            </table>
        </div>
    </form>

    <div class="d-flex justify-content-between mt-3">
        <button type="button" class="btn btn-secondary" onclick="$('#main-tab').tab('show')">
            <i class="bi bi-arrow-left"></i> Назад
        </button>
        <button type="button" class="btn btn-success" id="saveCadastralItems">
            <i class="bi bi-save"></i> Сохранить кадастровые номера
        </button>
    </div>
</div>