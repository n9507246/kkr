import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

export function create_smart_table(properties) {

    // Проверяем установлен ли id блока div для таблицы
    if (properties.debug && !properties.id) {
        console.warn(`CREATE_SMART_TABLE: Таблица не будет создана так как не указан id блока(div) в котором будет таблица`)
        return false;
    }

    //основные параметры
    const tableConfig = {
        height: properties.height ?? '400px',
        layout: "fitColumns",
        pagination: true,
        paginationMode: "remote",
        paginationSize: 10,
        paginationSizeSelector: [10, 20, 50, 100],
        paginationCounter: "rows",
        sortMode: "remote",
        filterMode: "remote",
    }

    //---------- AJAX -------------
        
        //проверяем параметр ajaxURL и устанавливаем адрес и тип ответа
        if (properties.ajaxURL){ 
            
            //URL с которого должны прийте данные таблицы
            tableConfig.ajaxURL = properties.ajaxURL,

            // Тип ответа от серавера - сопоставляем JSON от сервера с полями таблицы
            tableConfig.ajaxResponse = function(url, params, response) {
                if (properties.debug)
                    console.log('ajaxResponse', response);
                return { data: response.data, last_page: response.last_page };
            }
        }     

        //если параметр ajaxURL не установлен выводим предупреждение
        else{ 
            console.warn('CREATE_SMART_TABLE: не указан URL для AJAX запроса, в таблице не будут отображаться строки')
        }
    //-------------------------------


    //---------- Колонки ------------

    // инициализируем список колонок
    let columnsList = [];
    // состояние видимости колонок хранится в localStorage 
    const visiableStateColumns = JSON.parse(localStorage.getItem(`tabulator-${properties.id}-columns`) || "{}");

    // Формируем колонки с унаказиние дополнительных параметров
    if (properties.columns) { columnsList = properties.columns.map(col => (
        {
            // минимальная ширина колонки
            minWidth: 120,    

            // колонки всегда растягиваются (одинаково)
            widthGrow: 1,     

            // устанавливаем видимость колонки(информация из localStorage)
            visible: visiableStateColumns[col.field] !== undefined ? visiableStateColumns[col.field] : true, 

            // остальные параметры из колонки
            ...col,
        }
    ));
    } 
    //если пользователь не установил список колонок выводим ошибку
    else console.warn('CREATE_SMART_TABLE: Таблица не будет корректно отображаться т.к. не указаны колонки для таблицы')
    

    // Добавляем колонку действий
    if (properties.editUrl || properties.deleteUrl) {
        columnsList.push({
            title: "Действия",
            field: "actions",
            headerSort: false,
            hozAlign: "center",
            width: 120,
            frozen: true,
            formatter: function(cell) {
                const row = cell.getRow().getData();
                let buttons = '';

                if (properties.editUrl)
                    buttons += `<a href="${properties.editUrl}/${row.id}" class="btn btn-outline-warning btn-sm py-0 px-1 me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>`;

                if (properties.deleteUrl)
                    buttons += `<button class="btn btn-outline-danger btn-sm py-0 px-1 delete-btn" data-id="${row.id}">
                                    <i class="bi bi-trash"></i>
                                </button>`;

                return buttons;
            }
        });
    }

    tableConfig.columns = columnsList;

    // Создаем таблицу
    const table = new Tabulator(`#${properties.id}`, tableConfig);

    //Управление колонками
    if (properties.controll_column_visiable) {
        // Функция для сохранения состояния колонок
        function saveColumnState() {
            const columns = table.getColumns();
            const state = {};
            
            columns.forEach(column => {
                const def = column.getDefinition();
                // Сохраняем только для полей, которые не являются служебными
                if (def.field && def.field !== 'actions' && def.field !== 'id') {
                    state[def.field] = column.isVisible();
                }
            });
            
            localStorage.setItem(`tabulator-${properties.id}-columns`, JSON.stringify(state));
            
            if (properties.debug) {
                console.log('Сохраненное состояние колонок:', state);
            }
        }

        // Функция обновления чекбоксов
        function updateColumnCheckboxes() {
            const container = document.getElementById("columnCheckboxes");
            if (!container) return;
            
            container.innerHTML = "";

            table.getColumns().forEach(column => {
                const def = column.getDefinition();
                // Пропускаем служебные колонки
                if (!def.title || def.field === 'id' || def.field === 'actions') return;

                const div = document.createElement("div");
                div.className = "dropdown-item-checkbox";
                const isVisible = column.isVisible();

                const fieldName = def.field.replace(/\./g, '_');
                div.innerHTML = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="check_${fieldName}" ${isVisible ? "checked" : ""}>
                        <label class="form-check-label" for="check_${fieldName}">${def.title}</label>
                    </div>
                `;

                const checkbox = div.querySelector("input");
                checkbox.addEventListener("change", function() {
                    if (this.checked) {
                        column.show();
                    } else {
                        column.hide();
                    }
                    
                    // Сохраняем состояние после изменения
                    saveColumnState();
                    
                    updateHiddenCount();
                    setTimeout(() => table.redraw(true), 10);
                });
                
                container.appendChild(div);
            });
            
            updateHiddenCount();
        }

        function updateHiddenCount() {
            const checkboxes = document.querySelectorAll("#columnCheckboxes input");
            const hidden = Array.from(checkboxes).filter(i => !i.checked).length;
            const badge = document.getElementById("hiddenColumnsCount");
            if (badge) {
                badge.textContent = hidden;
                badge.style.display = hidden > 0 ? "inline-block" : "none";
            }
        }

        // Инициализация управления колонками
        if (properties.controll_column_visiable) {
            table.on("tableBuilt", function() {
                setTimeout(() => {
                    updateColumnCheckboxes();
                }, 100);
            });

            // Добавляем кнопку сброса
            const resetBtn = document.getElementById("resetColumnState");
            if (resetBtn) {
                resetBtn.addEventListener("click", function() {
                    // Очищаем сохраненное состояние
                    localStorage.removeItem(`tabulator-${properties.id}-columns`);
                    
                    // Показываем все колонки
                    table.getColumns().forEach(column => {
                        const def = column.getDefinition();
                        if (def.title && def.field !== 'id' && def.field !== 'actions') {
                            column.show();
                        }
                    });
                    
                    // Обновляем чекбоксы
                    updateColumnCheckboxes();
                    
                    // Сохраняем новое состояние (все колонки видимы)
                    saveColumnState();
                    
                    setTimeout(() => table.redraw(true), 50);
                });
            }
        }

        // Также сохраняем состояние при изменении колонок через интерфейс Tabulator
        table.on("columnMoved", saveColumnState);
        table.on("columnResized", saveColumnState);
        table.on("columnVisibilityChanged", saveColumnState);
    }

    return table;
}