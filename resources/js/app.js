

// Объект для управления видимостью колонок
const controllColumnVisiable = {

    table: null,                          // Ссылка на таблицу Tabulator
    contoll_visiable_columns: null,       // DOM-элемент с чекбоксами для управления видимостью

    /**
     * Инициализация управления видимостью колонок
     * @param {Object} table - экземпляр таблицы Tabulator
     */
    init: function(table) {
        
        this.table = table;

        // Получаем элемент с нашим выпадающим списком колонок
        this.contoll_visiable_columns = document.querySelector(
            `[to-smart-table="${this.table.getElement().id}"][role="controll_column_visiable"]`
        );
    
        // Если элемент не найден - выводим предупреждение и завершаем инициализацию
        if (!this.contoll_visiable_columns) {
            console.warn('CREATE_SMART_TABLE: Список отображения колонок не может быть создан так как не найден элемент в HTML role="controll_column_visiable" to-smart-table="' + properties.id + '"');
            return;
        }
        
        // Сохраняем состояние при изменениях колонок
        // this.table.on("columnMoved", () => this.saveColumnState());                 // При перемещении
        // this.table.on("columnResized", () => this.saveColumnState());               // При изменении размера
        this.table.on("columnVisibilityChanged", () => this.saveColumnState());     // При изменении видимости
        
        // Инициализация после построения таблицы
        this.onTableBuilt(() => {   
            this.createColumnCheckboxes(this.table.getColumns());  // Создаем чекбоксы для колонок
        });
        
        // Добавляем кнопку сброса настроек видимости колонок
        this.initResetButton();
    },

    /**
     * Устанавливает обработчик события построения таблицы
     * @param {Function} callback - функция, которая выполнится после построения таблицы
     */
    onTableBuilt: function(callback) {
        this.table.on("tableBuilt", () => {
            setTimeout(() => {
                callback.call(this);
            }, 100);
        });
    },

    /**
     * Перерисовывает таблицу с небольшой задержкой
     */
    tableRedraw: function() {
        setTimeout(() => this.table.redraw(true), 10);
    },

    /**
     * Создает чекбоксы для управления видимостью колонок
     * @param {Array} tableColumnList - массив колонок таблицы
     */
    createColumnCheckboxes: function(tableColumnList) {
        // Находим выпадающее меню (в нем будем создавать чекбоксы)
        const columnList = this.contoll_visiable_columns.querySelector("#columnCheckboxes");
        
        if (!columnList) {
            console.warn('CREATE_SMART_TABLE: Не найден элемент #columnCheckboxes');
            return;
        }
        
        // Очищаем контейнер
        columnList.innerHTML = "";
        
        // Создаем чекбоксы для каждой колонки
        tableColumnList.forEach(tableColumn => {
            const columnParams = tableColumn.getDefinition();  // Получаем параметры колонки
            
            // Пропускаем служебные колонки (id и действия)
            // if (!columnParams.title || columnParams.field === 'id' || columnParams.field === 'actions') return;
            
            // Создаем безопасное имя поля для использования в HTML-идентификаторах (заменяем точки на подчеркивания)
            const fieldName = columnParams.field.replace(/\./g, '_');
            const isVisible = tableColumn.isVisible();  // Проверяем текущую видимость колонки
            
            // Создаем элемент чекбокса
            const checkboxItem = this.createCheckboxItem(columnParams, fieldName, isVisible);
            const checkbox = checkboxItem.querySelector('input');
            
            // Обработчик изменения состояния чекбокса
            checkbox.addEventListener('change', (event) => {
                // Показываем или скрываем колонку в зависимости от состояния чекбокса
                if (event.target.checked) {
                    tableColumn.show();
                } else {
                    tableColumn.hide();
                }
                
                // Сохраняем состояние после изменения
                // this.saveColumnState();
                
                // Перерисовываем таблицу с небольшой задержкой
                setTimeout(() => this.table.redraw(true), 10);
            });
            
            // Добавляем чекбокс в список
            columnList.appendChild(checkboxItem);
        });
    },

    /**
     * Создает DOM-элемент чекбокса для колонки
     * @param {Object} columnParams - параметры колонки
     * @param {string} fieldName - безопасное имя поля для id
     * @param {boolean} isVisible - видимость колонки
     * @returns {HTMLElement} - DOM-элемент чекбокса
     */
    createCheckboxItem: function(columnParams, fieldName, isVisible) {
        const item = document.createElement('div');
        item.className = 'dropdown-item-checkbox';
        
        item.innerHTML = `
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    id="check_${fieldName}" 
                    ${isVisible ? 'checked' : ''}
                >
                <label 
                    class="form-check-label" 
                    for="check_${fieldName}"
                >
                    ${escapeHtml(columnParams.title)}
                </label>
            </div>
        `;
        
        return item;
    },

    /**
     * Сохраняет состояние видимости колонок в localStorage
     */
    saveColumnState: function() {
        const columns = this.table.getColumns();
        const state = {};
        
        columns.forEach(column => {
            const def = column.getDefinition();
            // Сохраняем состояние для всех колонок кроме служебных
            if (def.field && def.field !== 'actions' && def.field !== 'id') {
                state[def.field] = column.isVisible();
            }
        });
        
        localStorage.setItem(`tabulator-${properties.id}-columns`, JSON.stringify(state));
        
        if (properties.debug) {
            console.log('Сохраненное состояние колонок:', state);
        }
    },

    /**
     * Инициализирует кнопку сброса настроек видимости колонок
     */
    initResetButton: function() {
        const resetBtn = document.getElementById("resetColumnState");
        if (resetBtn) {
            resetBtn.addEventListener("click", () => {
                this.resetColumns();
            });
        }
    },

    /**
     * Сбрасывает настройки видимости колонок к состоянию по умолчанию
     */
    resetColumns: function() {
        // Очищаем сохраненное состояние из localStorage
        localStorage.removeItem(`tabulator-${properties.id}-columns`);
        
        // Показываем все колонки
        this.table.getColumns().forEach(column => {
            column.show();
        });
        
        // Обновляем чекбоксы (пересоздаем их)
        this.createColumnCheckboxes(this.table.getColumns());
        
        // Сохраняем новое состояние (все колонки видимы)
        // this.saveColumnState();
        
        // Перерисовываем таблицу
        setTimeout(() => this.table.redraw(true), 50);
    }
};

/**
 * Экранирует HTML-спецсимволы для предотвращения XSS-атак
 * @param {string} text - текст для экранирования
 * @returns {string} - экранированный текст
 */
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Создает умную таблицу с использованием Tabulator
 * @param {Object} properties - параметры таблицы
 * @returns {Object|boolean} - экземпляр таблицы Tabulator или false в случае ошибки
 */
export function create_smart_table(properties) {

    // Проверяем установлен ли id блока div для таблицы
    if (properties.debug && !properties.id) {
        console.warn(`CREATE_SMART_TABLE: Таблица не будет создана так как не указан id блока(div) в котором будет таблица`)
        return false;
    }

    // Основные параметры таблицы
    const tableConfig = {
        height: properties.height ?? '400px',          // Высота таблицы
        layout: "fitColumns",                           // Автоматическое распределение колонок
        pagination: true,                               // Включаем пагинацию
        paginationMode: "remote",                       // Серверная пагинация
        paginationSize: 10,                             // Записей на странице
        paginationSizeSelector: [10, 20, 50, 100],      // Возможные варианты записей на странице
        paginationCounter: "rows",                       // Отображение счетчика записей
        sortMode: "remote",                              // Серверная сортировка
        filterMode: "remote",                            // Серверная фильтрация
    }

    //---------- AJAX НАСТРОЙКИ -------------
        
    // Проверяем параметр ajaxURL и устанавливаем адрес и тип ответа
    if (properties.ajaxURL){ 
        
        // URL с которого должны прийти данные таблицы
        tableConfig.ajaxURL = properties.ajaxURL;

        // Обработка ответа от сервера - преобразуем JSON в формат Tabulator
        tableConfig.ajaxResponse = function(url, params, response) {
            if (properties.debug)
                console.log('ajaxResponse', response);
            // Возвращаем данные и информацию о последней странице
            return { 
                data: response.data, 
                last_page: response.last_page 
            };
        }
    }     

    // Если параметр ajaxURL не установлен выводим предупреждение
    else { 
        console.warn('CREATE_SMART_TABLE: не указан URL для AJAX запроса, в таблице не будут отображаться строки')
    }
    //-------------------------------


    //---------- НАСТРОЙКА КОЛОНОК ------------

    // Инициализируем список колонок
    let columnsList = [];
    
    // Загружаем сохраненное состояние видимости колонок из localStorage
    const visiableStateColumns = JSON.parse(localStorage.getItem(`tabulator-${properties.id}-columns`) || "{}");

    // Формируем колонки с указанием дополнительных параметров
    if (properties.columns) { 
        columnsList = properties.columns.map(col => ({
            // Минимальная ширина колонки
            minWidth: 120,    

            // Колонки всегда растягиваются (одинаково)
            widthGrow: 1,     

            // Устанавливаем видимость колонки (информация из localStorage)
            // Если в localStorage есть значение - используем его, иначе true
            visible: visiableStateColumns[col.field] !== undefined ? visiableStateColumns[col.field] : true, 

            // Остальные параметры из переданной конфигурации колонки
            ...col,
        }));
    } 
    // Если пользователь не установил список колонок выводим ошибку
    else {
        console.warn('CREATE_SMART_TABLE: Таблица не будет корректно отображаться т.к. не указаны колонки для таблицы');
    }
    

    // Добавляем колонку с действиями (редактирование/удаление)
    if (properties.editUrl || properties.deleteUrl) {
        columnsList.push({
            title: "Действия",                           // Заголовок колонки
            field: "actions",                             // Поле в данных
            headerSort: false,                            // Отключаем сортировку
            hozAlign: "center",                            // Выравнивание по центру
            width: 120,                                    // Ширина колонки
            frozen: true,                                  // Фиксируем колонку при прокрутке
            // Форматтер для отображения кнопок действий
            formatter: function(cell) {
                const row = cell.getRow().getData();      // Получаем данные строки
                let buttons = '';

                // Кнопка редактирования
                if (properties.editUrl)
                    buttons += `<a href="${properties.editUrl}/${row.id}" class="btn btn-outline-warning btn-sm py-0 px-1 me-1">
                                    <i class="bi bi-pencil"></i>
                                </a>`;

                // Кнопка удаления
                if (properties.deleteUrl)
                    buttons += `<button class="btn btn-outline-danger btn-sm py-0 px-1 delete-btn" data-id="${row.id}">
                                    <i class="bi bi-trash"></i>
                                </button>`;

                return buttons;
            }
        });
    }

    // Добавляем колонки в конфигурацию таблицы
    tableConfig.columns = columnsList;
    
    //-------- РУССКАЯ ЛОКАЛИЗАЦИЯ -----------------

    tableConfig.locale = "ru";                            // Устанавливаем русскую локаль
    tableConfig.langs = {
        "ru": {
            "ajax": {
                "loading": "Загрузка...",                  // Текст загрузки
                "error": "Ошибка загрузки"                  // Текст ошибки
            },
            "pagination": {
                "page_size": "Показать",                    // Текст для выбора размера страницы
                "first": "<<",                               // Кнопка первой страницы
                "first_title": "Первая страница",            // Подсказка для первой страницы
                "last": ">>",                                 // Кнопка последней страницы
                "last_title": "Последняя страница",          // Подсказка для последней страницы
                "prev": "<",                                  // Кнопка предыдущей страницы
                "prev_title": "Предыдущая страница",         // Подсказка для предыдущей страницы
                "next": ">",                                  // Кнопка следующей страницы
                "next_title": "Следующая страница",          // Подсказка для следующей страницы
                "all": "Все",                                 // Текст "Все" для показа всех записей
                "counter": {
                    "showing": "Показано",                   // Текст для счетчика записей
                    "of": "из",
                    "rows": "записей",
                    "pages": "страниц"
                }
            }
        }
    };

    //---------------------------------------

    // Создаем экземпляр таблицы Tabulator
    const table = new Tabulator(`#${properties.id}`, tableConfig);

    // Запускаем управление видимостью колонок, если это указано в параметрах
    if (properties.controll_column_visiable) {
        controllColumnVisiable.init(table);
    }

    // Возвращаем экземпляр таблицы
    return table;
}