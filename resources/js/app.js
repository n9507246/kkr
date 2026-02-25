import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

export function create_smart_table(properties){
        // Определяем конфиг таблицы и устанавливаем некоторые поля
        if(properties.debug && !properties.id){
            console.warn(`CREATE_SMART_TABLE: Таблица не будет создана так как не указан id блока(div) в котором будет таблица`)
            return false;
        }

        const tableConfig = {
            height: properties.height ?? '400px',   // height - высота таблицы

            layout: "fitColumns",                   // fitColumns – колонки автоматически растягиваются по ширине таблицы, чтобы заполнить весь контейнер.
                                                    // Горизонтальная прокрутка появляется только если колонки не умещаются.

            ajaxURL: properties.ajaxURL,            // URL для AJAX-загрузки данных

            pagination: true,           // включаем пагинацию
            paginationMode: "remote",   // указываем что пагинация будет выполняться на сервере
            paginationSize: 10,         // указываем количество строк
            paginationSizeSelector: [10, 20, 50, 100], //передаем массив что бы можно было менять количество строк
            paginationCounter: "rows",  // Отображает счётчик строк ("rows" – количество записей на странице, можно использовать "pages" – страницы).

            sortMode: "remote",         // сортировка будет выполняться удаленно
            filterMode: "remote",       // фильтрация будет выполняться удаленно

            // --- Обработка ответа AJAX ---
            ajaxResponse: function(url, params, response) {
                
                if(properties.debug)
                    console.log('ajaxResponse', response);
                
                // Tabulator ожидает объект {data, last_page}
                // data – массив строк для текущей страницы
                // last_page – количество страниц (для remote pagination)
                return { data: response.data, last_page: response.last_page };
            }
        }
        // Добавляем колонки в конфиг

        // отсюда берутся список состояний колонок (поле visible)
        const storageKey = `tabulator-${properties.id}-columns`;
        const savedState = JSON.parse(localStorage.getItem(storageKey) || "{}");

        let columnsList = undefined;
        if(properties.columns) 
            columnsList = properties.columns.map(col => ({
                widthGrow: 1,      //что бы колонки растягивались когда отключаются соседнии (всегда растягиваются одинаково)
                minWidth: 120,     //что бы не ужимались до одного символа
                ...col,            //остальные параметры колонки взятой из параметров компонента
                visible: savedState[col.field] !== undefined ? savedState[col.field] : true  // устанавливаем скрыть или показать
            }));
        else {
            console.warn('CREATE_SMART_TABLE: Таблица не будет корректро отображаться т.к. не указаны колонки для таблицы')
            columnsList = []
        }
        // если указан путь для редактирования и удаления добавляем
        // столбец с кнопками действий над записью
        if(properties.editUrl || properties.deleteUrl) columnsList.push({ title: "Действия",
            field: "actions",
            headerSort: false,
            hozAlign: "center",
            width: 120,
            frozen: true,
            formatter: function(cell) {
                const row = cell.getRow().getData();
                let buttons = '';

                if(properties.editUrl)
                buttons += `<a href="{{ $editUrl }}/${row.id}" class="btn btn-outline-warning btn-sm py-0 px-1 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>`;
                

                if(properties.deleteUrl)
                buttons += `<button class="btn btn-outline-danger btn-sm py-0 px-1 delete-btn" data-id="${row.id}">
                                <i class="bi bi-trash"></i>
                            </button>`;


                return buttons;
            }
        });

        // сохраняем в объщей конфигурации список наших колонок
        tableConfig.columns = columnsList


    // Создаем таблицу
    const table = new Tabulator(`#${properties.id}`, tableConfig);




}