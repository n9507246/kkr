@props([
    'id' => null,  // ID таблицы
])


<button class="btn btn-outline-secondary btn-sm" type="button" id="toggle-filters" data-bs-toggle="collapse" data-bs-target="#filterPanel_{{ $id }}" aria-expanded="true" aria-controls="filterPanel_{{ $id }}">
    <i class="bi bi-filter me-1"></i> Фильтры
    <i class="bi bi-chevron-down ms-1" id="filterToggleIcon"></i>
</button>