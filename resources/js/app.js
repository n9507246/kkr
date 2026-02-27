const controllColumnVisiable = {
    table: null,                          // Ссылка на таблицу Tabulator
    contoll_visiable_columns: null,       // DOM-элемент с чекбоксами для управления видимостью
    resetButton: null,                    // DOM-элемент кнопки сброса настроек
    debug: false,                         // Режим отладки (по умолчанию выключен)

    /**
     * Инициализация управления видимостью колонок
     * @param {Object} table - экземпляр таблицы Tabulator
     * @param {boolean} debug - включить режим отладки
     */
    init: function(table, debug = false) {
        this.table = table;
        this.debug = debug; // Устанавливаем режим отладки
        
        this.log('Инициализация управления видимостью колонок');
        this.log(`ID таблицы: ${this.table.element.id}`);
        this.log(`Режим отладки: ${this.debug ? 'включен' : 'выключен'}`);

        // Получаем элемент с нашим выпадающим списком колонок
        this.contoll_visiable_columns = document.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable"]`
        );

        // Если элемент не найден - выводим предупреждение (только в режиме отладки) и завершаем инициализацию
        if (!this.contoll_visiable_columns) {
            this.logWarning(
                'CREATE SMART TABLE COLUMN VISIABLE: Список отображения колонок не может быть создан, ' +
                `так как не найден элемент в HTML role="controll_column_visiable" ` +
                `to-smart-table="${this.table.element.id}"`
            );
            return;
        }

        this.log('Элемент управления видимостью найден');

        // Сохраняем состояние при изменении видимости
        this.table.on("columnVisibilityChanged", () => {
            this.log('Событие columnVisibilityChanged');
            this.saveColumnState();
        });

        // Инициализация после построения таблицы
        this.onTableBuilt(() => {   
            this.log('Таблица построена, создаем чекбоксы для колонок');
            this.createColumnCheckboxes(this.table.getColumns());
        });
        
        // Инициализируем кнопку сброса настроек видимости колонок
        this.initResetButton();
    },

    /**
     * Логирование (только в режиме отладки)
     * @param {string} message - сообщение для логирования
     */
    log: function(message) {
        if (!this.debug) return;
        
        const prefix = '[ColumnVisibility]';
        const timestamp = new Date().toLocaleTimeString('ru-RU', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            fractionalSecondDigits: 3
        });
        
        console.log(`${prefix} [${timestamp}] ${message}`);
    },

    /**
     * Предупреждение (только в режиме отладки)
     * @param {string} message - сообщение для предупреждения
     */
    logWarning: function(message) {
        if (!this.debug) return;
        
        const prefix = '[ColumnVisibility]';
        const timestamp = new Date().toLocaleTimeString('ru-RU', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            fractionalSecondDigits: 3
        });
        
        console.warn(`${prefix} [${timestamp}] ${message}`);
    },

    /**
     * Устанавливает обработчик события построения таблицы
     * @param {Function} callback - функция, которая выполнится после построения таблицы
     */
    onTableBuilt: function(callback) {
        this.log('Ожидание события tableBuilt...');
        
        this.table.on("tableBuilt", () => {
            this.log('Событие tableBuilt получено');
            
            setTimeout(() => {
                this.log('Выполнение callback после задержки 100мс');
                callback.call(this);
            }, 100);
        });
    },

    /**
     * Создает чекбоксы для управления видимостью колонок
     * @param {Array} tableColumnList - массив колонок таблицы
     */
    createColumnCheckboxes: function(tableColumnList) {
        this.log(`Создание чекбоксов для ${tableColumnList.length} колонок`);
        
        // Находим выпадающее меню (в нем будем создавать чекбоксы)
        const columnList = this.contoll_visiable_columns.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable_list"]`
        );
        
        if (!columnList) {
            this.logWarning(
                'CREATE SMART TABLE COLUMN VISIABLE: Список отображения колонок не может быть создан, ' +
                `так как не найден элемент в HTML ` +
                `to-smart-table="${this.table.element.id}" ` +
                `role="controll_column_visiable_list который будет содержать чекбоксы ` +
                `для управления видимостью колонок`
            );
            return;
        }

        this.log('Контейнер для чекбоксов найден');

        // Очищаем контейнер
        columnList.innerHTML = "";
        this.log('Контейнер чекбоксов очищен');

        // Создаем чекбоксы для каждой колонки
        tableColumnList.forEach((tableColumn, index) => {
            const columnParams = tableColumn.getDefinition();
            
            this.log(
                `Обработка колонки ${index + 1}: ` +
                `field="${columnParams.field}", ` +
                `title="${columnParams.title}"`
            );

            // Создаем безопасное имя поля для использования в HTML-идентификаторах
            const fieldName = columnParams.field.replace(/\./g, '_');
            const isVisible = tableColumn.isVisible();
            
            this.log(`Колонка "${columnParams.title}" видимость: ${isVisible}`);
            
            // Создаем элемент чекбокса
            const checkboxItem = this.createCheckboxItem(columnParams, fieldName, isVisible);
            const checkbox = checkboxItem.querySelector('input');

            // Обработчик изменения состояния чекбокса
            checkbox.addEventListener('change', (event) => {
                this.log(
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
            this.log(`Чекбокс для колонки "${columnParams.title}" добавлен`);
        });

        this.log(`Создано ${tableColumnList.length} чекбоксов`);
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
        this.log('saveColumnState вызван');
        
        // Отменяем предыдущий вызов, если он был
        // Это ключевая часть debounce-механизма: каждый новый вызов отменяет предыдущий таймер
        if (this._saveTimeout) {
            this.log('Отмена предыдущего таймера сохранения');
            clearTimeout(this._saveTimeout);
        }
        
        // Устанавливаем новый таймер
        // Если в течение 100мс не будет нового вызова, выполнится сохранение
        this._saveTimeout = setTimeout(() => {
            this.log('Таймер сохранения сработал, начинаем сбор состояния');
            
            const columns = this.table.getColumns();
            this.log(`Получено ${columns.length} колонок для сохранения`);
            
            const state = {};

            // Собираем состояние видимости для всех колонок
            columns.forEach(column => {
                const def = column.getDefinition();
                
                // Исключаем служебные колонки из сохранения
                if (def.field && def.field !== 'actions' && def.field !== 'id') {
                    state[def.field] = column.isVisible();
                    this.log(
                        `Колонка "${def.field}": ${state[def.field] ? 'видима' : 'скрыта'}`
                    );
                }
            });

            // Сохраняем финальное состояние в localStorage
            const storageKey = `tabulator-${this.table.element.id}-columns`;
            const stateJson = JSON.stringify(state);
            
            localStorage.setItem(storageKey, stateJson);
            
            this.log(`Состояние колонок сохранено в localStorage: ${storageKey}`);
            this.log(`Размер сохраненных данных: ${stateJson.length} байт`);
            this.log(`Сохраненные данные: ${stateJson}`);

            // Очищаем ссылку на таймер после выполнения
            this._saveTimeout = null;

            // Перерисовываем таблицу
            setTimeout(() => {
                this.log('Перерисовка таблицы');
                this.table.redraw(true);
            }, 10);

        }, 100); // Задержка в 100мс после последнего изменения
    },

    /**
     * Инициализирует кнопку сброса настроек видимости колонок
     * Использует тот же паттерн селекторов, что и для других элементов управления
     */
    initResetButton: function() {
        this.log('Инициализация кнопки сброса');
        
        // Находим кнопку сброса, используя тот же паттерн атрибутов, что и для других элементов
        // Формат: [to-smart-table="ID_ТАБЛИЦЫ"][role="reset_column_visibility"]
        this.resetButton = document.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="reset_column_visibility"]`
        );
        
        // Проверяем, найдена ли кнопка
        if (!this.resetButton) {
            this.logWarning(
                'CREATE SMART TABLE COLUMN VISIABLE: Кнопка сброса настроек колонок не найдена. ' +
                `Убедитесь, что в HTML присутствует элемент с атрибутами: ` +
                `to-smart-table="${this.table.element.id}" и role="reset_column_visibility"`
            );
            return; // Завершаем инициализацию, если кнопка не найдена
        }
        
        this.log('Кнопка сброса найдена');
        
        // Навешиваем обработчик клика на кнопку
        this.resetButton.addEventListener("click", (event) => {
            this.log('Клик по кнопке сброса');
            
            // Предотвращаем стандартное поведение кнопки (если она внутри формы)
            event.preventDefault();
            
            // Вызываем сброс настроек колонок
            this.resetColumns();
        });
        
        this.log(`Кнопка сброса для таблицы "${this.table.element.id}" инициализирована`);
    },

    /**
     * Сбрасывает настройки видимости колонок к состоянию по умолчанию
     */
    resetColumns: function() {
        this.log('Начат сброс настроек колонок');
        
        try {
            // Очищаем сохраненное состояние из localStorage
            const storageKey = `tabulator-${this.table.element.id}-columns`;
            localStorage.removeItem(storageKey);
            
            this.log(`Сохраненное состояние колонок удалено из localStorage: ${storageKey}`);

            // Показываем все колонки
            const columns = this.table.getColumns();
            this.log(`Найдено ${columns.length} колонок для отображения`);
            
            columns.forEach((column, index) => {
                const def = column.getDefinition();
                this.log(`Отображение колонки ${index + 1}: ${def.field}`);
                column.show();
            });
            
            this.log(`Все ${columns.length} колонок таблицы отображены`);

            // Обновляем состояние чекбоксов в UI
            this.updateCheckboxesState();

            // Перерисовываем таблицу с небольшой задержкой для применения изменений
            setTimeout(() => {
                this.log('Перерисовка таблицы после сброса');
                this.table.redraw(true);
                this.log(`Таблица перерисована`);
            }, 50);

        } catch (error) {
            this.logWarning(
                `CREATE SMART TABLE COLUMN VISIABLE: Ошибка при сбросе колонок: ${error.message}`
            );
            this.log(`Стек ошибки: ${error.stack}`);
        }
    },

    /**
     * Обновляет состояние чекбоксов в соответствии с текущей видимостью колонок
     * Этот метод синхронизирует UI с фактическим состоянием таблицы
     */
    updateCheckboxesState: function() {
        this.log('Обновление состояния чекбоксов');
        
        // Получаем контейнер со списком чекбоксов
        const columnList = this.contoll_visiable_columns?.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="controll_column_visiable_list"]`
        );
        
        if (!columnList) {
            this.logWarning('CREATE SMART TABLE COLUMN VISIABLE: Список чекбоксов не найден для обновления');
            return;
        }

        // Получаем все колонки таблицы для проверки их видимости
        const tableColumns = this.table.getColumns();
        this.log(`Найдено ${tableColumns.length} колонок для синхронизации с чекбоксами`);
        
        let updatedCount = 0;
        
        // Для каждой колонки обновляем соответствующий чекбокс
        tableColumns.forEach(tableColumn => {
            const def = tableColumn.getDefinition();
            if (!def.field) {
                this.log(`Колонка без field пропущена`);
                return;
            }
            
            const fieldName = def.field.replace(/\./g, '_');
            const checkbox = columnList.querySelector(`#check_${fieldName}`);
            
            if (checkbox) {
                const isVisible = tableColumn.isVisible();
                const oldState = checkbox.checked;
                
                // Устанавливаем состояние чекбокса в соответствии с видимостью колонки
                checkbox.checked = isVisible;
                
                if (oldState !== isVisible) {
                    updatedCount++;
                    this.log(
                        `Чекбокс для колонки "${def.field}" изменен: ` +
                        `${oldState ? 'видима' : 'скрыта'} -> ` +
                        `${isVisible ? 'видима' : 'скрыта'}`
                    );
                }
            } else {
                this.log(`Чекбокс для колонки "${def.field}" не найден`);
            }
        });
        
        this.log(
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
        controllColumnVisiable.init(table, properties.debug);
    }

    // Запускаем экспорт в Excel, если это указано в параметрах
    if (properties.export_to_excel) {
        xlsxExporter.export(table);
    }
    

    // Возвращаем экземпляр таблицы
    return table;
}