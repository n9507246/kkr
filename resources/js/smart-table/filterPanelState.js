import * as bootstrap from 'bootstrap';

export function init_filter_panel_state(options = {}) {
    const tableId = options.tableId || options.id;

    if (!tableId) {
        return;
    }

    const panelId = options.panelId || `filterPanel_${tableId}`;
    const stateKey = options.storageKey || `smart-table:${tableId}:filter-panel-open`;
    const defaultOpen = options.defaultOpen ?? true;
    const panelEl = document.getElementById(panelId);
    const toggleBtn = document.querySelector(`[data-bs-target="#${panelId}"]`);

    if (!panelEl) {
        return;
    }

    const savedState = localStorage.getItem(stateKey);
    const shouldOpen = savedState === null ? defaultOpen : savedState === 'true';

    panelEl.classList.toggle('show', shouldOpen);
    if (toggleBtn) {
        toggleBtn.setAttribute('aria-expanded', shouldOpen ? 'true' : 'false');
    }

    if (typeof bootstrap !== 'undefined') {
        new bootstrap.Collapse(panelEl, { toggle: false });
    }

    panelEl.addEventListener('shown.bs.collapse', () => {
        localStorage.setItem(stateKey, 'true');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', 'true');
        }
    });

    panelEl.addEventListener('hidden.bs.collapse', () => {
        localStorage.setItem(stateKey, 'false');
        if (toggleBtn) {
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    });
}
