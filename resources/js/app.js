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
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable"]`
        );

        // Если элемент не найден - выводим предупреждение и завершаем инициализацию
        if (!this.contoll_visiable_columns) {
            console.warn('CREATE_SMART_TABLE: Список отображения колонок не может быть создан, так как не найден элемент в HTML role="controll_column_visiable" to-smart-table="' + properties.id + '"');
            return;
        }

        // Сохраняем состояние при изменении видимости
        this.table.on("columnVisibilityChanged", () => this.saveColumnState());

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
     * Создает чекбоксы для управления видимостью колонок
     * @param {Array} tableColumnList - массив колонок таблицы
     */
    createColumnCheckboxes: function(tableColumnList) {
        
        // Находим выпадающее меню (в нем будем создавать чекбоксы)
        const columnList = this.contoll_visiable_columns.querySelector(`[to-smart-table="${this.table.element.id}"][role="controll_column_visiable_list"]`);
        
        if (!columnList) {
            console.warn('CREATE_SMART_TABLE: Не найден элемент #columnCheckboxes');
            return;
        }

        columnList.innerHTML = "";

        // Очищаем контейнер только при первом создании
        if (!columnList.hasChildNodes()) {
            

            tableColumnList.forEach(tableColumn => {
                const columnParams = tableColumn.getDefinition();  // Получаем параметры колонки

                // Создаем безопасное имя поля для использования в HTML-идентификаторах
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
                });

                // Добавляем чекбокс в список
                columnList.appendChild(checkboxItem);
            });
        }
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
     * Сохраняет состояние видимости колонок в localStorage с debounce-механизмом
     * 
     * Зачем нужна задержка (debounce):
     * 1. Событие columnVisibilityChanged может вызываться несколько раз подряд 
     *    (например, при сбросе всех колонок или при групповых операциях)
     * 2. Без задержки мы бы сохраняли промежуточные состояния, что неэффективно
     * 3. Задержка позволяет дождаться завершения всех изменений и сохранить 
     *    только финальное состояние
     * 4. Уменьшает количество операций записи в localStorage (оптимизация)
     * 5. Предотвращает лишние перерендеры и повышает производительность
     * 
     * Механизм работы:
     * - При первом изменении устанавливается таймер на 100мс
     * - Если за это время происходит новое изменение, предыдущий таймер сбрасывается
     * - Только после того как изменения прекратились на 100мс, происходит сохранение
     * - Это гарантирует, что сохранится именно последнее состояние
     */
    saveColumnState: function() {
        // Отменяем предыдущий вызов, если он был
        // Это ключевая часть debounce-механизма: каждый новый вызов отменяет предыдущий таймер
        if (this._saveTimeout) {
            clearTimeout(this._saveTimeout);
        }
        
        // Устанавливаем новый таймер
        // Если в течение 100мс не будет нового вызова, выполнится сохранение
        this._saveTimeout = setTimeout(() => {
            const columns = this.table.getColumns();
            const state = {};

            // Собираем состояние видимости для всех колонок
            columns.forEach(column => {
                const def = column.getDefinition();
                // Исключаем служебные колонки из сохранения
                if (def.field && def.field !== 'actions' && def.field !== 'id') {
                    state[def.field] = column.isVisible();
                }
            });

            // Сохраняем финальное состояние в localStorage
            localStorage.setItem(`tabulator-${this.table.element.id}-columns`, JSON.stringify(state));

            // Очищаем ссылку на таймер после выполнения
            this._saveTimeout = null;

            //перерисовываем таблицу
            setTimeout(() => this.table.redraw(true), 10);

        }, 100); // Задержка в 100мс после последнего изменения
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
        localStorage.removeItem(`tabulator-${this.table.element.id}-columns`);

        // Показываем все колонки
        this.table.getColumns().forEach(column => {
            column.show();
        });

        // Обновляем чекбоксы (пересоздаем их)
        this.createColumnCheckboxes(this.table.getColumns());

        // Перерисовываем таблицу с небольшой задержкой
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

    const tableConfig = {}
    tableConfig.height = properties.height ?? '400px',          // Высота таблицы
    tableConfig.layout = "fitColumns",                          // Автоматическое распределение колонок

    tableConfig.pagination = true,                              // Включаем пагинацию
    tableConfig.paginationMode = "remote"                       // Серверная пагинация
    tableConfig.paginationSize = 10                             // Записей на странице
    tableConfig.paginationSizeSelector = [10, 20, 50, 100]      // Возможные варианты записей на странице
    tableConfig.paginationCounter = "rows"                      // Отображение счетчика записей
    tableConfig.sortMode = "remote"                             // Серверная сортировка
    tableConfig.filterMode = "remote"                           // Серверная фильтрация
    
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
                "prev_title": "Предыдущая страница",          // Подсказка для предыдущей страницы
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

    //----------------- ФИЛЬТРЫ (СДЕЛАТЬ РЕФАКТОРИНГ)----------------------

    const filterForm = document.querySelector(`[to-smart-table="${properties.id}"][role="fiters_table"]`);
    // В функции create_smart_table, после определения filterForm добавьте:

    // Ключ для хранения фильтров в localStorage
    const storageKey = `tabulator-${properties.id}-filters`;

    // Функция для сохранения фильтров
    function saveFiltersToStorage(formElement) {
        if (!formElement) return;
        
        const formData = new FormData(formElement);
        const data = {};
        formData.forEach((value, key) => {
            if (value) data[key] = value;
        });
        localStorage.setItem(storageKey, JSON.stringify(data));
    }

    // Функция для загрузки фильтров из localStorage
    function loadFiltersFromStorage(formElement) {
        if (!formElement) return;
        
        const saved = localStorage.getItem(storageKey);
        if (saved) {
            try {
                const data = JSON.parse(saved);
                Object.keys(data).forEach(key => {
                    const input = formElement.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = data[key];
                    }
                });
            } catch (e) {
                console.warn('Ошибка загрузки фильтров из localStorage:', e);
            }
        }
    }

    // Загружаем сохраненные фильтры в форму
    if (filterForm) {
        loadFiltersFromStorage(filterForm);
    }

    // Обновленная настройка ajaxParams
    if (properties.apply_filters) {
        tableConfig.ajaxParams = function() {
            const params = filterForm ? Object.fromEntries(new FormData(filterForm).entries()) : {};
            console.log("Параметры запроса:", {
                filters: params
            });
            return {
                filters: params
            };
        }
    }

    // Обновленный обработчик submit
    if (filterForm) {
        filterForm.addEventListener("submit", (e) => {
            e.preventDefault();
            saveFiltersToStorage(filterForm); // Сохраняем фильтры
            table.setData(); // Перезагружаем таблицу
        });

        // Добавляем кнопку сброса фильтров, если её нет в форме
        const resetBtn = filterForm.querySelector('[type="reset"]') || filterForm.querySelector('#reset-filters');
        if (resetBtn) {
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                filterForm.reset(); // Сбрасываем форму
                localStorage.removeItem(storageKey); // Удаляем сохраненные фильтры
                table.setData(); // Перезагружаем таблицу
            });
        }
    }

    // -----------------------------------------------------------------------------

    // Создаем экземпляр таблицы Tabulator
    const table = new Tabulator(`#${properties.id}`, tableConfig);

    // Запускаем управление видимостью колонок, если это указано в параметрах
    if (properties.controll_column_visiable) {
        controllColumnVisiable.init(table);
    }

    

    // Возвращаем экземпляр таблицы
    return table;
}