@props([
    'id',
    'ajaxUrl',
    'columns' => [],
    'height' => '500px',
    'method' => 'GET',
    'editUrl' => null,
    'deleteUrl' => null,
    'debug' => false
])
<style>
    /* Основные стили таблицы */
    #report-table { border-radius: 8px; overflow: hidden; border: 1px solid #eaecf0; width: 100%; }
    /* .tabulator-header { text-transform: uppercase; font-size: 0.75rem !important; background-color: #f8f9fa !important; }
    .tabulator-cell { font-size: 0.85rem !important; vertical-align: middle !important; } */

    /* Стили выпадающего списка колонок */
    .dropdown-menu { max-height: 500px !important; overflow-y: auto; min-width: 180px !important;  padding: 12px; z-index: 1060; max-height: 250px !important;}
    .dropdown-item-checkbox { padding: 6px 10px; border-radius: 4px; transition: background 0.2s; cursor: pointer; display: flex; align-items: center; }
    .dropdown-item-checkbox:hover { background-color: #f8f9fa; }
    .dropdown-item-checkbox input { cursor: pointer; margin-right: 12px; width: 16px; height: 16px; }
    .dropdown-item-checkbox label { cursor: pointer; flex: 1; margin: 0; font-size: 0.9rem; user-select: none; }

</style>
<div class="dropdown">
    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
        <i class="bi bi-layout-three-columns me-1"></i> Колонки
    </button>
    <div class="spisok-polonok dropdown-menu shadow border-0" aria-labelledby="columnDropdown" >
        <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
        <div id="columnCheckboxes"></div>
        <div class="dropdown-divider"></div>
        <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start" id="resetColumnState">
            <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
        </button>
    </div>
</div>

<div style="overflow-x:auto;">
    <div id="{{ $id }}"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    let preparedColumns = @json($columns).map(col => {
        return {
            widthGrow: 1,
            minWidth: 120,
            ...col,
        };
    });


    const table = new Tabulator("#{{ $id }}", {
        layout: "fitColumns",
        height: "{{ $height }}",

        ajaxURL: "{{ $ajaxUrl }}",
        ajaxConfig: "{{ $method }}",

        pagination: true,
        paginationMode: "remote",
        paginationSizeSelector: [10, 20, 50, 100],
        paginationCounter: "rows",

        sortMode: "remote",
        filterMode: "remote",

        paginationSize: 10,

        ajaxResponse: function(url, params, response) {
            @if($debug)
                console.log('ajaxResponse ==========> ', {
                data: response.data,
                last_page: response.last_page,
            })
            @endif
            return {
                data: response.data,
                last_page: response.last_page,
            };
        },

        columns: [
            ...preparedColumns,

            @if($editUrl || $deleteUrl)
            {
                title: "Действия",
                field: "actions",
                headerSort: false,
                hozAlign: "center",
                width: 120,
                frozen: true,
                formatter: function(cell){
                    const row = cell.getRow().getData();
                    let buttons = '';

                    @if($editUrl)
                        buttons += `
                            <a href="{{ $editUrl }}/${row.id}"
                               class="btn btn-outline-warning btn-sm py-0 px-1 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                        `;
                    @endif

                    @if($deleteUrl)
                        buttons += `
                            <button class="btn btn-outline-danger btn-sm py-0 px-1 delete-btn"
                                    data-id="${row.id}">
                                <i class="bi bi-trash"></i>
                            </button>
                        `;
                    @endif

                    return buttons;
                }
            }
            @endif
        ],
        locale: "ru",
        langs: {
            "ru": {
                "ajax": {
                    "loading": "Загрузка...",
                    "error": "Ошибка загрузки"
                },
                "pagination": {
                    "page_size": "Показать",
                    "first": "<<",
                    "first_title": "Первая страница",
                    "last": ">>",
                    "last_title": "Последняя страница",
                    "prev": "<",
                    "prev_title": "Предыдущая страница",
                    "next": ">",
                    "next_title": "Следующая страница",
                    "all": "Все",
                    "counter": {
                        "showing": "Показано",
                        "of": "из",
                        "rows": "записей",
                        "pages": "страниц"
                    }
                }
            }
        },
    });

    @if($deleteUrl)
    document.getElementById("{{ $id }}").addEventListener("click", function(e){
        const btn = e.target.closest(".delete-btn");
        if(!btn) return;

        const id = btn.dataset.id;

        if(confirm("Удалить запись?")){
            fetch("{{ $deleteUrl }}/" + id, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                },
            })
            .then(res => res.json())
            .then(res => {
                if(res.success){
                    table.replaceData();
                }
            });
        }
    });
    @endif

    table.on("tableBuilt", function() {
        const container = document.getElementById("columnCheckboxes");
        if (!container) return;
        container.innerHTML = "";

        table.getColumns().forEach(column => {
            const def = column.getDefinition();
            if (!def.title || def.field === 'id') return;

            const div = document.createElement("div");
            div.className = "dropdown-item-checkbox";
            const isVisible = column.isVisible();

            div.innerHTML = `
                <input type="checkbox" id="check_${def.field.replace(/\./g, '_')}" ${isVisible ? "checked" : ""}>
                <label for="check_${def.field.replace(/\./g, '_')}">${def.title}</label>
            `;

            div.querySelector("input").addEventListener("change", function() {
                if (this.checked) column.show();
                else column.hide();

                setTimeout(() => table.redraw(true), 10);
            });
            container.appendChild(div);
        });
    });


    // сброс списка отображения колонок
        document.getElementById("resetColumnState").addEventListener("click", () => {
            if(confirm("Сбросить все настройки колонок?")) {
                localStorage.removeItem("tabulator-realEstateTable_vFinal-columns");
                location.reload();
            }
        });
});
</script>
@endpush
