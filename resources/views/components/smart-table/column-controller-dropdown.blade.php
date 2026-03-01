
@props([
    'id' => null,  // ID таблицы
])


<div {{ $attributes->class(['spisok-kolonok', 'dropdown-menu', 'shadow', 'border-0']) }} aria-labelledby="columnDropdown">
    <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
    <div role="controll_column_visiable" to-smart-table="{{ $id }}"></div>
    <div class="dropdown-divider"></div>
    <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start mb-2" to-smart-table="{{ $id }}"  role="reset_column_visibility">
        <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
    </button>
</div>
