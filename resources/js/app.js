/**
 * Логгер с гибкой настройкой вывода сообщений
 */
const logger = {
    config: {
        enabled: false,           // Включен/выключен
        prefix: '',                // Префикс для всех сообщений
        showTimestamp: true,       // Показывать время
        showLevel: true,           // Показывать уровень сообщения
        levels: {                  // Какие уровни выводить
            log: true,
            info: true,
            warn: true,
            error: true,
            debug: true
        },
        timestampFormat: {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            fractionalSecondDigits: 3
        }
    },

    /**
     * Настройка логгера
     * @param {Object} options - параметры конфигурации
     */
    configure: function(options = {}) {
        // Рекурсивно обновляем конфигурацию
        this.config = this.mergeDeep(this.config, options);
        return this;
    },

    /**
     * Создает новый экземпляр логгера с собственной конфигурацией
     * @param {Object} options - параметры конфигурации для нового экземпляра
     * @returns {Object} новый экземпляр логгера
     */
    createInstance: function(options = {}) {
        const instance = Object.create(this);
        instance.config = this.mergeDeep({}, this.config);
        instance.configure(options);
        return instance;
    },

    /**
     * Глубокое слияние объектов
     * @param {Object} target - целевой объект
     * @param {Object} source - источник
     * @returns {Object} результат слияния
     */
    mergeDeep: function(target, source) {
        const result = { ...target };
        
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                result[key] = this.mergeDeep(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        
        return result;
    },

    /**
     * Форматирует сообщение для вывода
     * @param {string} level - уровень сообщения
     * @param {string} message - текст сообщения
     * @returns {string} отформатированное сообщение
     */
    formatMessage: function(level, message) {
        const parts = [];
        
        // Добавляем префикс
        if (this.config.prefix) {
            parts.push(this.config.prefix);
        }
        
        // Добавляем временную метку
        if (this.config.showTimestamp) {
            const timestamp = new Date().toLocaleTimeString('ru-RU', this.config.timestampFormat);
            parts.push(`[${timestamp}]`);
        }
        
        // Добавляем уровень сообщения
        if (this.config.showLevel) {
            parts.push(`[${level.toUpperCase()}]`);
        }
        
        // Добавляем само сообщение
        parts.push(message);
        
        return parts.join(' ');
    },

    /**
     * Проверяет, нужно ли выводить сообщение данного уровня
     * @param {string} level - уровень сообщения
     * @returns {boolean} true если нужно выводить
     */
    shouldLog: function(level) {
        return this.config.enabled && this.config.levels[level] === true;
    },

    // Методы логирования для разных уровней
    log: function(message) {
        if (this.shouldLog('log')) {
            console.log(this.formatMessage('log', message));
        }
    },

    info: function(message) {
        if (this.shouldLog('info')) {
            console.info(this.formatMessage('info', message));
        }
    },

    warn: function(message) {
        if (this.shouldLog('warn')) {
            console.warn(this.formatMessage('warn', message));
        }
    },

    error: function(message) {
        if (this.shouldLog('error')) {
            console.error(this.formatMessage('error', message));
        }
    },

    debug: function(message) {
        if (this.shouldLog('debug')) {
            console.debug(this.formatMessage('debug', message));
        }
    },

    /**
     * Универсальный метод логирования
     * @param {string} level - уровень сообщения
     * @param {string} message - текст сообщения
     */
    logLevel: function(level, message) {
        if (this.shouldLog(level)) {
            const method = console[level] || console.log;
            method.call(console, this.formatMessage(level, message));
        }
    }
};

/**
 * Управление видимостью колонок таблицы Tabulator
 */
const controllColumnVisiable = {
    table: null,                          // Ссылка на таблицу Tabulator
    contoll_visiable_columns: null,       // DOM-элемент с чекбоксами для управления видимостью
    resetButton: null,                    // DOM-элемент кнопки сброса настроек
    logger: null,                          // Экземпляр логгера

    /**
     * Инициализация управления видимостью колонок
     * @param {Object} table - экземпляр таблицы Tabulator
     * @param {Object} options - дополнительные параметры
     * @param {boolean} options.debug - включить режим отладки (все сообщения)
     * @param {Object} options.logger - экземпляр логгера
     */
    init: function(table, options = {}) {
        
        this.table = table;
        
        // Используем переданный логгер или создаем новый
        if (options.logger) {
            this.logger = options.logger;
        } else {
            this.logger = logger.createInstance({
                enabled: options.debug || false,
                prefix: '[CREATE SMART TABLE] [COLUMN VISIABLE]',
                showTimestamp: true,
                showLevel: true,
                levels: {
                    log: false,
                    info: false,
                    warn: options.debug || false,
                    error: options.debug || false,
                    debug: false
                }
            });
        }
        
        this.logger.debug('Инициализация управления видимостью колонок');
        this.logger.debug(`ID таблицы: ${this.table.element.id}`);
        this.logger.debug(`Режим отладки: ${options.debug ? 'включен' : 'выключен'}`);

        // Получаем элемент с нашим выпадающим списком колонок
        this.contoll_visiable_columns = document.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable"]`
        );

        // Если элемент не найден - логируем предупреждение и завершаем инициализацию
        if (!this.contoll_visiable_columns) {
            this.logger.warn(
                'Список отображения колонок не может быть создан, ' +
                `так как не найден элемент в HTML role="controll_column_visiable" ` +
                `to-smart-table="${this.table.element.id}"`
            );
            return;
        }

        this.logger.debug('Элемент управления видимостью найден');

        // Сохраняем состояние при изменении видимости
        this.table.on("columnVisibilityChanged", () => {
            this.logger.debug('Событие columnVisibilityChanged');
            this.saveColumnState();
        });

        // Инициализация после построения таблицы
        this.onTableBuilt(() => {   
            this.logger.debug('Таблица построена, создаем чекбоксы для колонок');
            this.createColumnCheckboxes(this.table.getColumns());
        });
        
        // Инициализируем кнопку сброса настроек видимости колонок
        this.initResetButton();
    },

    /**
     * Устанавливает обработчик события построения таблицы
     * @param {Function} callback - функция, которая выполнится после построения таблицы
     */
    onTableBuilt: function(callback) {
        this.logger.debug('Ожидание события tableBuilt...');
        
        this.table.on("tableBuilt", () => {
            this.logger.debug('Событие tableBuilt получено');
            
            setTimeout(() => {
                this.logger.debug('Выполнение callback после задержки 100мс');
                callback.call(this);
            }, 100);
        });
    },

    /**
     * Создает чекбоксы для управления видимостью колонок
     * @param {Array} tableColumnList - массив колонок таблицы
     */
    createColumnCheckboxes: function(tableColumnList) {
        this.logger.info(`Создание чекбоксов для ${tableColumnList.length} колонок`);
        
        // Находим выпадающее меню (в нем будем создавать чекбоксы)
        const columnList = this.contoll_visiable_columns.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable_list"]`
        );
        
        if (!columnList) {
            this.logger.warn(
                'CREATE SMART TABLE COLUMN VISIABLE: Список отображения колонок не может быть создан, ' +
                `так как не найден элемент в HTML ` +
                `to-smart-table="${this.table.element.id}" ` +
                `role="controll_column_visiable_list который будет содержать чекбоксы ` +
                `для управления видимостью колонок`
            );
            return;
        }

        this.logger.debug('Контейнер для чекбоксов найден');

        // Очищаем контейнер
        columnList.innerHTML = "";
        this.logger.debug('Контейнер чекбоксов очищен');

        // Создаем чекбоксы для каждой колонки
        tableColumnList.forEach((tableColumn, index) => {
            const columnParams = tableColumn.getDefinition();
            
            this.logger.debug(
                `Обработка колонки ${index + 1}: ` +
                `field="${columnParams.field}", ` +
                `title="${columnParams.title}"`
            );

            // Создаем безопасное имя поля для использования в HTML-идентификаторах
            const fieldName = columnParams.field.replace(/\./g, '_');
            const isVisible = tableColumn.isVisible();
            
            this.logger.debug(`Колонка "${columnParams.title}" видимость: ${isVisible}`);
            
            // Создаем элемент чекбокса
            const checkboxItem = this.createCheckboxItem(columnParams, fieldName, isVisible);
            const checkbox = checkboxItem.querySelector('input');

            // Обработчик изменения состояния чекбокса
            checkbox.addEventListener('change', (event) => {
                this.logger.info(
                    `Изменение видимости колонки "${columnParams.title}": ` +
                    `${event.target.checked ? 'показать' : 'скрыть'}`
                );
                
                // Показываем или скрываем колонку в зависимости от состояния чекбокса
                if (event.target.checked) {
                    tableColumn.show();
                } else {
                    tableColumn.hide();
                }
            });

            // Добавляем чекбокс в список
            columnList.appendChild(checkboxItem);
            this.logger.debug(`Чекбокс для колонки "${columnParams.title}" добавлен`);
        });

        this.logger.info(`Создано ${tableColumnList.length} чекбоксов`);
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
     */
    saveColumnState: function() {
        this.logger.debug('saveColumnState вызван');
        
        // Отменяем предыдущий вызов, если он был
        if (this._saveTimeout) {
            this.logger.debug('Отмена предыдущего таймера сохранения');
            clearTimeout(this._saveTimeout);
        }
        
        // Устанавливаем новый таймер
        this._saveTimeout = setTimeout(() => {
            this.logger.debug('Таймер сохранения сработал, начинаем сбор состояния');
            
            const columns = this.table.getColumns();
            this.logger.debug(`Получено ${columns.length} колонок для сохранения`);
            
            const state = {};

            // Собираем состояние видимости для всех колонок
            columns.forEach(column => {
                const def = column.getDefinition();
                
                // Исключаем служебные колонки из сохранения
                if (def.field && def.field !== 'actions' && def.field !== 'id') {
                    state[def.field] = column.isVisible();
                    this.logger.debug(
                        `Колонка "${def.field}": ${state[def.field] ? 'видима' : 'скрыта'}`
                    );
                }
            });

            // Сохраняем финальное состояние в localStorage
            const storageKey = `tabulator-${this.table.element.id}-columns`;
            const stateJson = JSON.stringify(state);
            
            localStorage.setItem(storageKey, stateJson);
            
            this.logger.info(`Состояние колонок сохранено в localStorage: ${storageKey}`);
            this.logger.debug(`Размер сохраненных данных: ${stateJson.length} байт`);
            this.logger.debug(`Сохраненные данные: ${stateJson}`);

            // Очищаем ссылку на таймер после выполнения
            this._saveTimeout = null;

            // Перерисовываем таблицу
            setTimeout(() => {
                this.logger.debug('Перерисовка таблицы');
                this.table.redraw(true);
            }, 10);

        }, 100);
    },

    /**
     * Инициализирует кнопку сброса настроек видимости колонок
     */
    initResetButton: function() {
        this.logger.debug('Инициализация кнопки сброса');
        
        // Находим кнопку сброса
        this.resetButton = document.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="reset_column_visibility"]`
        );
        
        // Проверяем, найдена ли кнопка
        if (!this.resetButton) {
            this.logger.warn(
                'Кнопка сброса настроек колонок не найдена. ' +
                `Убедитесь, что в HTML присутствует элемент с атрибутами: ` +
                `to-smart-table="${this.table.element.id}" и role="reset_column_visibility"`
            );
            return;
        }
        
        this.logger.debug('Кнопка сброса найдена');
        
        // Навешиваем обработчик клика на кнопку
        this.resetButton.addEventListener("click", (event) => {
            this.logger.info('Клик по кнопке сброса');
            
            // Предотвращаем стандартное поведение кнопки
            event.preventDefault();
            
            // Вызываем сброс настроек колонок
            this.resetColumns();
        });
        
        this.logger.info(`Кнопка сброса для таблицы "${this.table.element.id}" инициализирована`);
    },

    /**
     * Сбрасывает настройки видимости колонок к состоянию по умолчанию
     */
    resetColumns: function() {
        this.logger.info('Начат сброс настроек колонок');
        
        try {
            // Очищаем сохраненное состояние из localStorage
            const storageKey = `tabulator-${this.table.element.id}-columns`;
            localStorage.removeItem(storageKey);
            
            this.logger.info(`Сохраненное состояние колонок удалено из localStorage: ${storageKey}`);

            // Показываем все колонки
            const columns = this.table.getColumns();
            this.logger.debug(`Найдено ${columns.length} колонок для отображения`);
            
            columns.forEach((column, index) => {
                const def = column.getDefinition();
                this.logger.debug(`Отображение колонки ${index + 1}: ${def.field}`);
                column.show();
            });
            
            this.logger.info(`Все ${columns.length} колонок таблицы отображены`);

            // Обновляем состояние чекбоксов в UI
            this.updateCheckboxesState();

            // Перерисовываем таблицу
            setTimeout(() => {
                this.logger.debug('Перерисовка таблицы после сброса');
                this.table.redraw(true);
                this.logger.info(`Таблица перерисована`);
            }, 50);

        } catch (error) {
            this.logger.error(
                `CREATE SMART TABLE COLUMN VISIABLE: Ошибка при сбросе колонок: ${error.message}`
            );
            this.logger.debug(`Стек ошибки: ${error.stack}`);
        }
    },

    /**
     * Обновляет состояние чекбоксов в соответствии с текущей видимостью колонок
     */
    updateCheckboxesState: function() {
        this.logger.debug('Обновление состояния чекбоксов');
        
        // Получаем контейнер со списком чекбоксов
        const columnList = this.contoll_visiable_columns?.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable_list"]`
        );
        
        if (!columnList) {
            this.logger.warn('CREATE SMART TABLE COLUMN VISIABLE: Список чекбоксов не найден для обновления');
            return;
        }

        // Получаем все колонки таблицы
        const tableColumns = this.table.getColumns();
        this.logger.debug(`Найдено ${tableColumns.length} колонок для синхронизации с чекбоксами`);
        
        let updatedCount = 0;
        
        // Для каждой колонки обновляем соответствующий чекбокс
        tableColumns.forEach(tableColumn => {
            const def = tableColumn.getDefinition();
            if (!def.field) {
                this.logger.debug(`Колонка без field пропущена`);
                return;
            }
            
            const fieldName = def.field.replace(/\./g, '_');
            const checkbox = columnList.querySelector(`#check_${fieldName}`);
            
            if (checkbox) {
                const isVisible = tableColumn.isVisible();
                const oldState = checkbox.checked;
                
                // Устанавливаем состояние чекбокса
                checkbox.checked = isVisible;
                
                if (oldState !== isVisible) {
                    updatedCount++;
                    this.logger.debug(
                        `Чекбокс для колонки "${def.field}" изменен: ` +
                        `${oldState ? 'видима' : 'скрыта'} -> ` +
                        `${isVisible ? 'видима' : 'скрыта'}`
                    );
                }
            } else {
                this.logger.debug(`Чекбокс для колонки "${def.field}" не найден`);
            }
        });
        
        this.logger.info(
            `Состояние чекбоксов обновлено ` +
            `(изменено ${updatedCount} из ${tableColumns.length})`
        );
    }
};

// Вспомогательная функция для экранирования HTML (если не определена)
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}


/**
 * Объект для экспорта таблицы Tabulator в Excel с автоподбором ширины колонок
 */
/**
 * Объект для экспорта таблицы Tabulator в Excel с автоподбором ширины колонок
 */
/**
 * Объект для экспорта таблицы Tabulator в Excel с автоподбором ширины колонок
 */
const excelExporter = {
    table: null,
    logger: null,
    ajaxURL: null,
    exportButtonId: 'export-excel-btn',

    init(table, options = {}) {
        this.table = table;
        this.logger = options.logger || null;
        this.ajaxURL = options.ajaxURL || null;

        if (options.exportButtonId) {
            this.exportButtonId = options.exportButtonId;
        }

        const btn = document.getElementById(this.exportButtonId);
        if (!btn) {
            this.logger?.warn(`Excel export button "${this.exportButtonId}" not found`);
            return;
        }

        btn.addEventListener('click', () => this.export());
        this.logger?.info('Excel exporter initialized');
    },

    async export() {
        try {
            this.logger?.info('Starting Excel export');

            const columns = this.table.getColumnLayout()
                .filter(col => col.visible !== false && col.field && col.field !== 'actions');

            const data = await this.fetchAllData();

            this.buildWorkbook(columns, data);

        } catch (e) {
            this.logger?.error('Export error: ' + e.message);
        }
    },

    async fetchAllData() {
        if (!this.ajaxURL) {
            return this.table.getData();
        }

        const params = new URLSearchParams();
        let currentParams = {};

        // 1. Извлекаем динамические параметры (фильтры) из настроек таблицы
        // В твоем логе это функция, которая собирает FormData из filterForm
        if (typeof this.table.options.ajaxParams === "function") {
            currentParams = this.table.options.ajaxParams();
        } else if (this.table.options.ajaxParams) {
            currentParams = this.table.options.ajaxParams;
        }

        // 2. Добавляем фильтры в URL
        // Если твоя функция возвращает { filters: { field: value } }, обрабатываем это
        if (currentParams.filters) {
            Object.keys(currentParams.filters).forEach(key => {
                const value = currentParams.filters[key];
                if (value) {
                    params.append(`filters[${key}]`, value);
                }
            });
        } else {
            // На случай, если параметры лежат в корне объекта
            Object.keys(currentParams).forEach(key => {
                if (currentParams[key]) params.append(key, currentParams[key]);
            });
        }

        // 3. Добавляем параметры пагинации для выгрузки "всего"
        params.set('page', 1);
        params.set('size', 9999999999);

        const finalUrl = this.ajaxURL.includes('?') 
            ? `${this.ajaxURL}&${params.toString()}` 
            : `${this.ajaxURL}?${params.toString()}`;

        this.logger?.info('Final export URL with filters: ' + finalUrl);

        const response = await fetch(finalUrl, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`Ошибка сервера: ${response.status}`);
        }

        const json = await response.json();
        
        // Возвращаем данные (учитывая структуру ответа Laravel/Tabulator)
        return json.data || [];
    },
    buildWorkbook(columns, data) {

        const header = columns.map(c => c.title || c.field);
        const rows = data.map(row =>
            columns.map(c => this.getNestedValue(row, c.field))
        );

        const worksheetData = [header, ...rows];

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(worksheetData);

        ws['!cols'] = this.calculateWidths(columns, data);

        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'export.xlsx');

        this.logger?.info('Excel file saved');
    },

    calculateWidths(columns, data) {
        return columns.map(col => {

            let max = this.visualLength(col.title || '');

            data.forEach(row => {
                const value = this.getNestedValue(row, col.field);
                if (value !== null && value !== undefined) {
                    const len = this.visualLength(String(value));
                    if (len > max) max = len;
                }
            });

            return { wch: Math.min(Math.max(max + 3, 10), 80) };
        });
    },

    visualLength(text) {
        let length = 0;

        for (let char of text) {
            if (/[А-Яа-яЁё]/.test(char)) length += 1.5;
            else if (/[A-Z]/.test(char)) length += 1.2;
            else length += 1;
        }

        return Math.ceil(length);
    },

    getNestedValue(obj, path) {
        return path.split('.').reduce((acc, key) => acc ? acc[key] : null, obj);
    }
};
export function create_smart_table(properties) {
    
    properties.debug = true
    const isDebug = !!properties.debug;
    
    // Создаем ЕДИНЫЙ экземпляр логгера для всей таблицы
    const mainLogger = logger.createInstance({
        
        enabled: true,
        prefix: '[SmartTable]',
        showTimestamp: true,
        showLevel: true,
        levels: {
            // Если isDebug === false, эти уровни станут false и не будут выводиться
            log: isDebug,
            info: isDebug,
            debug: isDebug,
            // Предупреждения и ошибки оставляем включенными всегда
            warn: true, 
            error: true
        }
    });

    mainLogger.debug('Начало создания таблицы');
    mainLogger.debug(`Параметры: ${JSON.stringify(properties)}`);

    // Проверяем установлен ли id блока div для таблицы
    if (!properties.id) {
        mainLogger.warn('Таблица не будет создана так как не указан id блока(div) в котором будет таблица');
        return false;
    }

    if (document.getElementById(properties.id) === null) {
        mainLogger.warn(`Таблица не будет создана так как не найден элемент с id="${properties.id}" в котором должна быть таблица`);
        return false;
    }

    mainLogger.debug(`Элемент с id="${properties.id}" найден`);

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
    
    tableConfig.dependencies = properties.dependencies

    //---------- AJAX НАСТРОЙКИ -------------
        
    // Проверяем параметр ajaxURL и устанавливаем адрес и тип ответа
    if (properties.ajaxURL) { 
        
        // URL с которого должны прийти данные таблицы
        tableConfig.ajaxURL = properties.ajaxURL;
        mainLogger.debug(`AJAX URL установлен: ${properties.ajaxURL}`);

        // Обработка ответа от сервера - преобразуем JSON в формат Tabulator
        tableConfig.ajaxResponse = function(url, params, response) {
            mainLogger.debug('Получен ответ от сервера');
            mainLogger.debug(`Параметры запроса: ${JSON.stringify(params)}`);
            mainLogger.debug(`Размер ответа: ${response.data?.length || 0} записей`);
            
            // Возвращаем данные и информацию о последней странице
            return { 
                data: response.data, 
                last_page: response.last_page 
            };
        }
    } else { 
        mainLogger.warn('Не указан URL для AJAX запроса, в таблице не будут отображаться строки');
    }
    //-------------------------------

    //---------- НАСТРОЙКА КОЛОНОК ------------

    // Инициализируем список колонок
    let columnsList = [];
    
    // Загружаем сохраненное состояние видимости колонок из localStorage
    const visiableStateColumns = JSON.parse(localStorage.getItem(`tabulator-${properties.id}-columns`) || "{}");
    mainLogger.debug(`Загружено состояние видимости колонок: ${JSON.stringify(visiableStateColumns)}`);

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
        
        mainLogger.debug(`Сформировано ${columnsList.length} колонок`);
    } else {
        mainLogger.warn('Таблица не будет корректно отображаться т.к. не указаны колонки для таблицы');
    }

    // Добавляем колонку с действиями (редактирование/удаление)
    if (properties.editUrl || properties.deleteUrl) {
        mainLogger.debug('Добавление колонки с действиями');
        
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

    mainLogger.debug('Русская локализация применена');


    mainLogger.debug('Инициализация фильтров');

    // Находим форму фильтров
    const filterForm = document.querySelector(`[to-smart-table="${properties.id}"][role="fiters_table"]`);
    
    if (!filterForm) {
        mainLogger.warn(
            'Форма фильтров не найдена. ' +
            `Убедитесь, что в HTML присутствует элемент с атрибутами: ` +
            `to-smart-table="${properties.id}" и role="fiters_table"`
        );
    } else {
        mainLogger.debug('Форма фильтров найдена');

        // Ключ для хранения фильтров в localStorage
        const storageKey = `tabulator-${properties.id}-filters`;
        mainLogger.debug(`Ключ localStorage для фильтров: ${storageKey}`);

        // Функция для сохранения фильтров
        function saveFiltersToStorage() {
            mainLogger.debug('Сохранение фильтров в localStorage');
            
            const formData = new FormData(filterForm);
            const data = {};
            
            formData.forEach((value, key) => {
                if (value) {
                    data[key] = value;
                    mainLogger.debug(`Поле фильтра: ${key} = ${value}`);
                }
            });

            const dataJson = JSON.stringify(data);
            localStorage.setItem(storageKey, dataJson);
            
            mainLogger.info(`Фильтры сохранены в localStorage: ${storageKey}`);
            mainLogger.debug(`Размер сохраненных данных: ${dataJson.length} байт`);
            
            return data;
        }

        // Функция для загрузки фильтров из localStorage
        function loadFiltersFromStorage() {
            mainLogger.debug('Загрузка фильтров из localStorage');
            
            const saved = localStorage.getItem(storageKey);
            
            if (!saved) {
                mainLogger.debug('Сохраненные фильтры не найдены');
                return null;
            }

            try {
                const data = JSON.parse(saved);
                mainLogger.debug(`Загруженные данные: ${JSON.stringify(data)}`);
                
                let loadedCount = 0;
                
                Object.keys(data).forEach(key => {
                    const input = filterForm.querySelector(`[name="${key}"]`);
                    if (input) {
                        input.value = data[key];
                        loadedCount++;
                        mainLogger.debug(`Поле "${key}" загружено: ${data[key]}`);
                    } else {
                        mainLogger.warn(`Поле "${key}" не найдено в форме фильтров`);
                    }
                });
                
                mainLogger.info(`Загружено ${loadedCount} фильтров из localStorage`);
                return data;
                
            } catch (e) {
                mainLogger.error(`Ошибка загрузки фильтров из localStorage: ${e.message}`);
                return null;
            }
        }

        // Загружаем сохраненные фильтры в форму
        loadFiltersFromStorage();

        // Настройка ajaxParams для включения фильтров
        if (properties.apply_filters) {
            mainLogger.debug('Настройка ajaxParams для фильтров');
            
            tableConfig.ajaxParams = function() {
                const formData = new FormData(filterForm);
                const params = {};
                
                formData.forEach((value, key) => {
                    if (value) params[key] = value;
                });
                
                mainLogger.debug(`Параметры фильтров для запроса: ${JSON.stringify(params)}`);
                
                return {
                    filters: params
                };
            }
        }

        // Обработчик отправки формы
        filterForm.addEventListener("submit", (e) => {
            e.preventDefault();
            mainLogger.info('Отправка формы фильтров');
            
            saveFiltersToStorage();
            mainLogger.debug('Фильтры сохранены, перезагрузка таблицы');
            
            table.setData();
        });

        // Поиск кнопки сброса
        const resetBtn = filterForm.querySelector('[type="reset"]') || filterForm.querySelector('#reset-filters');
        
        if (resetBtn) {
            mainLogger.debug('Кнопка сброса фильтров найдена');
            
            resetBtn.addEventListener('click', (e) => {
                e.preventDefault();
                mainLogger.info('Сброс фильтров');
                
                filterForm.reset();
                localStorage.removeItem(storageKey);
                mainLogger.debug(`Фильтры удалены из localStorage: ${storageKey}`);
                
                table.setData();
                mainLogger.info('Фильтры сброшены, таблица перезагружена');
            });
        } else {
            mainLogger.debug('Кнопка сброса фильтров не найдена');
        }

        mainLogger.debug('Инициализация фильтров завершена');
    }

    // -----------------------------------------------------------------------------

    mainLogger.debug('Создание экземпляра таблицы Tabulator');
    
    // Создаем экземпляр таблицы Tabulator
    const table = new Tabulator(`#${properties.id}`, tableConfig);

    mainLogger.debug('Таблица Tabulator создана');

    // Запускаем управление видимостью колонок, если это указано в параметрах
    if (properties.controll_column_visiable) {
        mainLogger.debug('Инициализация управления видимостью колонок');
        // Передаем основной логгер в управление видимостью
        controllColumnVisiable.init(table, { 
            debug: properties.debug,
            logger: mainLogger 
        });
    }

    // Запускаем экспорт в Excel, если это указано в параметрах
    if  (properties.export_to_excel) {
excelExporter.init(table, {
    logger: mainLogger,
    ajaxURL: properties.ajaxURL || null,
    exportButtonId: properties.exportButtonId || 'export-excel-btn'
});
    }
    
    mainLogger.info(`Таблица с id="${properties.id}" успешно создана`);
    
    // Возвращаем экземпляр таблицы
    return table;
}