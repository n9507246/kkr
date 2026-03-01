@props([
    'id' => null,
    'id_table' => null,
])

@php
    $tableId = $id ?? $id_table;
@endphp

<button
    id="export-excel-btn-{{ $tableId }}"
    to-smart-table="{{ $tableId }}"
    role="export_excel"
    {{ $attributes->class(['btn', 'btn-success', 'btn-sm']) }}
    type="button"
>
    <i class="bi bi-file-earmark-excel me-1"></i> Экспорт в Excel
</button>
