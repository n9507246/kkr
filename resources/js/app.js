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
     * @param {Object} options.loggerConfig - конфигурация логгера
     */
    init: function(table, options = {}) {
        
          this.table = table;
        // Создаем экземпляр логгера с конфигурацией
          
    
        const debug = options.debug ?? false;
        
        // Создаем экземпляр логгера с конфигурацией
        this.initLogger({ debug });
        
        
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
     * Инициализация логгера с настройками
     * @param {Object} options - параметры инициализации
     */
    initLogger: function(options) {
        const debug = options.debug || false;
        
        this.logger = logger.createInstance({
            enabled: debug,
            prefix: '[CREATE SMART TABLE] [COLUMN VISIABLE]',
            showTimestamp: true,
            showLevel: true,
            levels: {
                log: false,
                info: false,
                warn: debug,
                error: debug,
                debug: false
            }
        });
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
const xlsxExporter = {
    /**
     * Инициализирует экспортер и навешивает обработчик на кнопку
     * @param {Object} table - экземпляр таблицы Tabulator
     */
    export: function(table) {
        // Сохраняем ссылку на таблицу для использования в обработчике
        this.table = table;
        
        // Находим кнопку экспорта по ID
        const exportButton = document.getElementById('test-button-excel');
        
        // Проверяем, существует ли кнопка на странице
        if (!exportButton) {
            console.warn('CREATE_SMART_TABLE: Кнопка экспорта с ID "test-button-excel" не найдена');
            return;
        }
        
        // Навешиваем обработчик клика на кнопку
        exportButton.addEventListener('click', () => {
            this.exportToExcel();
        });
        
        console.log('Экспортер инициализирован, обработчик навешен на кнопку');
    },
    
    /**
     * Выполняет экспорт таблицы в Excel с автоподбором ширины колонок
     */
    exportToExcel: function() {
        
        // Проверяем, существует ли таблица
        if (!this.table) {
            console.error('Таблица не инициализирована');
            return;
        }
        
        // Вызываем метод download таблицы Tabulator
        this.table.download("xlsx", "данные.xlsx", {
            // Название листа в Excel-файле
            sheetName: "Лист1",
            
            /**
             * Обработчик для модификации книги Excel перед сохранением
             * @param {Object} workbook - объект книги Excel от SheetJS
             * @returns {Object} модифицированная книга
             */
            documentProcessing: (workbook) => this.autoFitColumns(workbook)
        });
        
        console.log('Экспорт в Excel запущен');
    },
    
    /**
     * Автоматически подбирает ширину колонок по содержимому
     * @param {Object} workbook - объект книги Excel от SheetJS
     * @returns {Object} модифицированная книга с настроенными колонками
     */
    autoFitColumns: function(workbook) {
        try {
            // Получаем название первого листа в книге
            const firstSheetName = workbook.SheetNames[0];
            
            // Получаем сам лист по названию
            const sheet = workbook.Sheets[firstSheetName];
            
            // Проверяем, существует ли лист
            if (!sheet) {
                console.warn('Лист не найден, пропускаем автоподбор');
                return workbook;
            }
            
            // Проверяем, есть ли в листе данные
            if (!sheet['!ref']) {
                console.warn('Лист пустой, пропускаем автоподбор');
                return workbook;
            }
            
            // ============================================================
            // Определяем диапазон данных в листе
            // ============================================================
            // !ref содержит ссылку на диапазон, например "A1:C10"
            const range = XLSX.utils.decode_range(sheet['!ref']);
            
            // Массив для хранения настроек ширины колонок
            const columnWidths = [];
            
            // ============================================================
            // Проходим по каждой колонке
            // ============================================================
            for (let colIndex = range.s.c; colIndex <= range.e.c; colIndex++) {
                // Переменная для хранения максимальной длины в текущей колонке
                let maxLength = 0;
                
                // ============================================================
                // Проходим по каждой строке в текущей колонке
                // ============================================================
                for (let rowIndex = range.s.r; rowIndex <= range.e.r; rowIndex++) {
                    // Создаем объект с координатами ячейки
                    const cellCoordinates = {
                        c: colIndex, // колонка (0-индекс)
                        r: rowIndex  // строка (0-индекс)
                    };
                    
                    // Получаем ссылку на ячейку в формате "A1", "B2" и т.д.
                    const cellAddress = XLSX.utils.encode_cell(cellCoordinates);
                    
                    // Получаем ячейку из листа
                    const cell = sheet[cellAddress];
                    
                    // Если ячейка существует и содержит значение
                    if (cell && cell.v !== undefined && cell.v !== null) {
                        // Преобразуем значение в строку и считаем длину
                        const cellValue = String(cell.v);
                        const valueLength = cellValue.length;
                        
                        // Обновляем максимальную длину, если нашли более длинное значение
                        if (valueLength > maxLength) {
                            maxLength = valueLength;
                        }
                    }
                }
                
                // ============================================================
                // Устанавливаем ширину колонки
                // ============================================================
                // Добавляем запас в 2 символа для лучшей читаемости
                // Минимальная ширина - 5 символов, чтобы колонка не схлопнулась
                const columnWidth = Math.max(maxLength + 2, 5);
                columnWidths[colIndex] = { wch: columnWidth };
            }
            
            // ============================================================
            // Применяем настройки ширины к листу
            // ============================================================
            // Специальное свойство !cols отвечает за ширину колонок в Excel
            sheet['!cols'] = columnWidths;
            
            console.log('Автоподбор ширины колонок выполнен успешно');
            
        } catch (error) {
            // Ловим и логируем ошибки, чтобы не прерывать экспорт
            console.error('Ошибка при автоподборе ширины колонок:', error);
        }
        
        // Всегда возвращаем workbook, даже если была ошибка
        return workbook;
    }
};


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

    if(properties.id && document.getElementById(properties.id) === null) {
        console.warn(`CREATE_SMART_TABLE: Таблица не будет создана так как не найден элемент с id="${properties.id}" в котором должна быть таблица`)
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
    
    tableConfig.dependencies = properties.dependencies

    //---------- AJAX НАСТРОЙКИ -------------
        
    // Проверяем параметр ajaxURL и устанавливаем адрес и тип ответа
    if (properties.ajaxURL){ 
        
        // URL с которого должны прийти данные таблицы
        tableConfig.ajaxURL = properties.ajaxURL;

        // Обработка ответа от сервера - преобразуем JSON в формат Tabulator
        tableConfig.ajaxResponse = function(url, params, response) {
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
        controllColumnVisiable.init(table, { debug: properties.debug });
    }

    // Запускаем экспорт в Excel, если это указано в параметрах
    if (properties.export_to_excel) {
        xlsxExporter.export(table);
    }
    

    // Возвращаем экземпляр таблицы
    return table;
}