@props([
    'id' => null,  // ID таблицы
])

<div class="collapse" id="filterPanel_{{ $id }}">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body bg-light border-bottom">
            <form id="filter-form" class="row g-3" to-smart-table="{{ $id }}" role="fiters_table">
                {{ $filters }}
                <div class="col-12 text-end mt-2">
                    <button type="button" id="reset-filters" class="btn btn-light btn-sm border me-2">Сбросить</button>
                    <button type="submit" class="btn btn-primary btn-sm px-4">Найти</button>
                </div>
            </form>
        </div>
    </div>
</div>