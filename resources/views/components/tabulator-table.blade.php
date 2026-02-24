@props([
    'id',
    'ajaxUrl',
    'columns' => [],
    'height' => '500px',
    'editUrl' => null,
    'deleteUrl' => null,
    'debug' => false
])
<!-- Cписок отображаемых полей -->
    <div class="dropdown">
        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="columnDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside">
            <i class="bi bi-layout-three-columns me-1"></i> Колонки
        </button>
        <div class="spisok-polonok dropdown-menu shadow border-0" aria-labelledby="columnDropdown">
            <div class="fw-bold small mb-2 border-bottom px-3 py-2">Отображение полей:</div>
            <div id="columnCheckboxes"></div>
            <div class="dropdown-divider"></div>
            <button type="button" class="btn btn-link btn-sm text-decoration-none w-100 text-start mb-2" id="resetColumnState">
                <i class="bi bi-arrow-counterclockwise"></i> Сбросить вид
            </button>
        </div>
    </div>

<!-- Таблица -->
<div class='mt-2' style="overflow-x:auto;">
    <div id="{{ $id }}"></div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // Определяем конфиг таблицы и устанавливаем некоторые поля

        const tableConfig = {
            height: "{{ $height }}",    // height - высота таблицы

            layout: "fitColumns",       // fitColumns – колонки автоматически растягиваются по ширине таблицы, чтобы заполнить весь контейнер.
                                        // Горизонтальная прокрутка появляется только если колонки не умещаются.

            ajaxURL: "{{ $ajaxUrl }}",  // URL для AJAX-загрузки данных

            pagination: true,           // включаем пагинацию
            paginationMode: "remote",   // указываем что пагинация будет выполняться на сервере
            paginationSize: 10,         // указываем количество строк
            paginationSizeSelector: [10, 20, 50, 100], //передаем массив что бы можно было менять количество строк
            paginationCounter: "rows",  // Отображает счётчик строк ("rows" – количество записей на странице, можно использовать "pages" – страницы).

            sortMode: "remote",         // сортировка будет выполняться удаленно
            filterMode: "remote",       // фильтрация будет выполняться удаленно

            // --- Обработка ответа AJAX ---
            ajaxResponse: function(url, params, response) {
                @if($debug)
                console.log('ajaxResponse', response);
                @endif
                // Tabulator ожидает объект {data, last_page}
                // data – массив строк для текущей страницы
                // last_page – количество страниц (для remote pagination)
                return { data: response.data, last_page: response.last_page };
            }
        }


    // Добавляем колонки в конфиг

        // отсюда берутся список состояний колонок (поле visible)
        const storageKey = "tabulator-{{ $id }}-columns";
        const savedState = JSON.parse(localStorage.getItem(storageKey) || "{}");

        const columnsList = @json($columns).map(col => ({
            widthGrow: 1,      //что бы колонки растягивались когда отключаются соседнии (всегда растягиваются одинаково)
            minWidth: 120,     //что бы не ужимались до одного символа
            ...col,            //остальные параметры колонки взятой из параметров компонента
            visible: savedState[col.field] !== undefined ? savedState[col.field] : true  // устанавливаем скрыть или показать
        }));

        // если указан путь для редактирования и удаления добавляем
        // столбец с кнопками действий над записью
        @if($editUrl || $deleteUrl) columnsList.push({ title: "Действия",
            field: "actions",
            headerSort: false,
            hozAlign: "center",
            width: 120,
            frozen: true,
            formatter: function(cell) {
                const row = cell.getRow().getData();
                let buttons = '';

                @if($editUrl)
                buttons += `<a href="{{ $editUrl }}/${row.id}" class="btn btn-outline-warning btn-sm py-0 px-1 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>`;
                @endif

                @if($deleteUrl)
                buttons += `<button class="btn btn-outline-danger btn-sm py-0 px-1 delete-btn" data-id="${row.id}">
                                <i class="bi bi-trash"></i>
                            </button>`;
                @endif

                return buttons;
            }
        });
        @endif

        // сохраняем в объщей конфигурации список наших колонок
        tableConfig.columns = columnsList


    // Создаем таблицу
    const table = new Tabulator("#{{ $id }}", tableConfig);




    // --- Колонки: отображение и сохранение ---
    function renderColumnCheckboxes() {
        const container = document.getElementById("columnCheckboxes");
        container.innerHTML = "";

        table.getColumns().forEach(column => {
            const def = column.getDefinition();
            if(!def.title || def.field === 'id') return;

            const div = document.createElement("div");
            div.className = "dropdown-item-checkbox";
            const isVisible = column.isVisible();

            div.innerHTML = `
                <input type="checkbox" id="check_${def.field.replace(/\./g, '_')}" ${isVisible ? "checked" : ""}>
                <label for="check_${def.field.replace(/\./g, '_')}">${def.title}</label>
            `;

            const input = div.querySelector("input");
            input.addEventListener("change", function() {
                this.checked ? column.show() : column.hide();

                // --- сохраняем состояние колонок ---
                const state = {};
                table.getColumns().forEach(col => {
                    const colDef = col.getDefinition();
                    if(colDef.field) state[colDef.field] = col.isVisible();
                });
                localStorage.setItem(storageKey, JSON.stringify(state));

                setTimeout(() => table.redraw(true), 10);
            });

            container.appendChild(div);
        });
    }

    table.on("tableBuilt", renderColumnCheckboxes);

    // --- Сброс состояния колонок ---
    document.getElementById("resetColumnState").addEventListener("click", () => {
        if(confirm("Сбросить все настройки колонок?")) {
            localStorage.removeItem(storageKey);
            location.reload();
        }
    });

    // --- Удаление записи ---
    @if($deleteUrl)
    document.getElementById("{{ $id }}").addEventListener("click", function(e) {
        const btn = e.target.closest(".delete-btn");
        if(!btn) return;

        if(confirm("Удалить запись?")) {
            fetch("{{ $deleteUrl }}/" + btn.dataset.id, {
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Accept": "application/json",
                },
            })
            .then(res => res.json())
            .then(res => {
                if(res.success) table.replaceData();
            });
        }
    });
    @endif

});
</script>
@endpush


<style>
    /* Основные стили таблицы */
    #report-table { border-radius: 8px; overflow: hidden; border: 1px solid #eaecf0; width: 100%; }
    /* */.tabulator-header { text-transform: uppercase; font-size: 0.75rem !important; background-color: #f8f9fa !important; }
    .tabulator-cell { font-size: 0.85rem !important; vertical-align: middle !important; }

    /* Стили выпадающего списка колонок */
    .dropdown-menu { max-height: 500px !important; overflow-y: auto; min-width: 180px !important;  padding: 0; z-index: 1060; max-height: 250px !important;}
    .dropdown-item-checkbox { padding: 6px 10px; border-radius: 4px; transition: background 0.2s; cursor: pointer; display: flex; align-items: center; }
    .dropdown-item-checkbox:hover { background-color: #f8f9fa; }
    .dropdown-item-checkbox input { cursor: pointer; margin-right: 12px; width: 16px; height: 16px; }
    .dropdown-item-checkbox label { cursor: pointer; flex: 1; margin: 0; font-size: 0.9rem; user-select: none; }

</style>
