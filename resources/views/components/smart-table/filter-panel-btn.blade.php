@props([
    'id' => null,  // ID таблицы
])


<button class="btn btn-outline-secondary btn-sm" type="button" id="toggle-filters-{{ $id }}" data-bs-toggle="collapse" data-bs-target="#filterPanel_{{ $id }}" aria-expanded="true" aria-controls="filterPanel_{{ $id }}">
    <i class="bi bi-filter me-1"></i> Фильтры
    <i class="bi bi-chevron-down ms-1" id="filterToggleIcon-{{ $id }}"></i>
</button>

<script>
(function () {
    try {
        const tableId = @json($id);
        const stateKey = `smart-table:${tableId}:filter-panel-open`;
        const savedState = localStorage.getItem(stateKey);
        const shouldOpen = savedState === null ? true : savedState === 'true';
        const btn = document.getElementById(`toggle-filters-${tableId}`);

        if (btn) {
            btn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
        }
    } catch (e) {
        // ignore localStorage access issues
    }
})();
</script>
