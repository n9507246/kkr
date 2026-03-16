import { logger } from './logger';
import { escapeHtml } from './helpers';

export const columnVisibilityController = {
    table: null,
    columnCheckboxesContainer: null,
    resetButton: null,
    logger: null,
    storageKeyBase: null,

    init: function(table, options = {}) {
        this.table = table;
        this.storageKeyBase = options.storageKeyBase || this.table.element.id;

        if (options.logger) {
            this.logger = options.logger;
        } else {
            this.logger = logger.createInstance({
                enabled: options.debug || false,
                prefix: '[CREATE SMART TABLE] [COLUMN VISIBILITY]',
                showTimestamp: true,
                showLevel: true,
                levels: {
                    log: false,
                    info: false,
                    warn: options.debug || false,
                    error: options.debug || false,
                    debug: false,
                },
            });
        }

        this.logger.debug('Инициализация управления видимостью колонок');
        this.logger.debug(`ID таблицы: ${this.table.element.id}`);
        this.logger.debug(`Режим отладки: ${options.debug ? 'включен' : 'выключен'}`);

        const tableId = this.table.element.id;
        this.columnCheckboxesContainer = document.querySelector(
            `[to-smart-table="${tableId}"][role="controll_column_visiable"], ` +
            `[to-smart-table="${tableId}"][role="control_column_visibility"]`
        );

        if (!this.columnCheckboxesContainer) {
            this.logger.warn(
                'Контейнер для чекбоксов не найден. Убедитесь, что создан элемент с атрибутами ' +
                `role="controll_column_visiable" (или role="control_column_visibility") ` +
                `и to-smart-table="${tableId}"`
            );
            return;
        }
        this.logger.debug('Контейнер для чекбоксов найден и сохранен');

        this.table.on('columnVisibilityChanged', () => {
            this.logger.debug('Событие columnVisibilityChanged');
            this.saveColumnState();
        });

        this.onTableBuilt(() => {
            this.logger.debug('Таблица построена, создаем чекбоксы для колонок');
            this.createColumnCheckboxes(this.table.getColumns());
        });

        this.initResetButton();
    },

    onTableBuilt: function(callback) {
        this.logger.debug('Ожидание события tableBuilt...');

        this.table.on('tableBuilt', () => {
            this.logger.debug('Событие tableBuilt получено');

            setTimeout(() => {
                this.logger.debug('Выполнение callback после задержки 100мс');
                callback.call(this);
            }, 100);
        });
    },

    createColumnCheckboxes: function(tableColumnList) {
        this.logger.info(`Создание чекбоксов для ${tableColumnList.length} колонок`);
        this.logger.debug('Контейнер для чекбоксов найден');

        this.columnCheckboxesContainer.innerHTML = '';
        this.logger.debug('Контейнер чекбоксов очищен');

        tableColumnList.forEach((tableColumn, index) => {
            const columnParams = tableColumn.getDefinition();

            this.logger.debug(
                `Обработка колонки ${index + 1}: ` +
                `field="${columnParams.field}", ` +
                `title="${columnParams.title}"`
            );

            const fieldName = columnParams.field.replace(/\./g, '_');
            const isVisible = tableColumn.isVisible();

            this.logger.debug(`Колонка "${columnParams.title}" видимость: ${isVisible}`);

            const checkboxItem = this.createCheckboxItem(columnParams, fieldName, isVisible);
            const checkbox = checkboxItem.querySelector('input');

            checkbox.addEventListener('change', (event) => {
                this.logger.info(
                    `Изменение видимости колонки "${columnParams.title}": ` +
                    `${event.target.checked ? 'показать' : 'скрыть'}`
                );

                if (event.target.checked) {
                    tableColumn.show();
                } else {
                    tableColumn.hide();
                }
            });

            this.columnCheckboxesContainer.appendChild(checkboxItem);
            this.logger.debug(`Чекбокс для колонки "${columnParams.title}" добавлен`);
        });

        this.logger.info(`Создано ${tableColumnList.length} чекбоксов`);
    },

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

    saveColumnState: function() {
        this.logger.debug('saveColumnState вызван');

        if (this._saveTimeout) {
            this.logger.debug('Отмена предыдущего таймера сохранения');
            clearTimeout(this._saveTimeout);
        }

        this._saveTimeout = setTimeout(() => {
            this.logger.debug('Таймер сохранения сработал, начинаем сбор состояния');

            const columns = this.table.getColumns();
            this.logger.debug(`Получено ${columns.length} колонок для сохранения`);

            const state = {};

            columns.forEach(column => {
                const def = column.getDefinition();

                if (def.field && def.field !== 'actions' && def.field !== 'id') {
                    state[def.field] = column.isVisible();
                    this.logger.debug(`Колонка "${def.field}": ${state[def.field] ? 'видима' : 'скрыта'}`);
                }
            });

            const storageKey = `tabulator-${this.storageKeyBase}-columns`;
            const stateJson = JSON.stringify(state);

            localStorage.setItem(storageKey, stateJson);

            this.logger.info(`Состояние колонок сохранено в localStorage: ${storageKey}`);
            this.logger.debug(`Размер сохраненных данных: ${stateJson.length} байт`);
            this.logger.debug(`Сохраненные данные: ${stateJson}`);

            this._saveTimeout = null;

            setTimeout(() => {
                this.logger.debug('Перерисовка таблицы');
                this.table.redraw(true);
            }, 10);
        }, 100);
    },

    initResetButton: function() {
        this.logger.debug('Инициализация кнопки сброса');

        this.resetButton = document.querySelector(
            `[to-smart-table="${this.table.element.id}"][role="reset_column_visibility"]`
        );

        if (!this.resetButton) {
            this.logger.warn(
                'Кнопка сброса настроек колонок не найдена. ' +
                `Убедитесь, что в HTML присутствует элемент с атрибутами: ` +
                `to-smart-table="${this.table.element.id}" и role="reset_column_visibility"`
            );
            return;
        }

        this.logger.debug('Кнопка сброса найдена');

        this.resetButton.addEventListener('click', (event) => {
            this.logger.info('Клик по кнопке сброса');
            event.preventDefault();
            this.resetColumns();
        });

        this.logger.info(`Кнопка сброса для таблицы "${this.table.element.id}" инициализирована`);
    },

    resetColumns: function() {
        this.logger.info('Начат сброс настроек колонок');

        try {
            const storageKey = `tabulator-${this.storageKeyBase}-columns`;
            localStorage.removeItem(storageKey);

            this.logger.info(`Сохраненное состояние колонок удалено из localStorage: ${storageKey}`);

            const columns = this.table.getColumns();
            this.logger.debug(`Найдено ${columns.length} колонок для отображения`);

            columns.forEach((column, index) => {
                const def = column.getDefinition();
                this.logger.debug(`Отображение колонки ${index + 1}: ${def.field}`);
                column.show();
            });

            this.logger.info(`Все ${columns.length} колонок таблицы отображены`);
            this.updateCheckboxesState();

            setTimeout(() => {
                this.logger.debug('Перерисовка таблицы после сброса');
                this.table.redraw(true);
                this.logger.info('Таблица перерисована');
            }, 50);
        } catch (error) {
            this.logger.error(
                `CREATE SMART TABLE COLUMN VISIBILITY: Ошибка при сбросе колонок: ${error.message}`
            );
            this.logger.debug(`Стек ошибки: ${error.stack}`);
        }
    },

    updateCheckboxesState: function() {
        this.logger.debug('Обновление состояния чекбоксов');

        const columnList = this.columnCheckboxesContainer;

        if (!columnList) {
            this.logger.warn('Список чекбоксов не найден для обновления');
            return;
        }

        const tableColumns = this.table.getColumns();
        this.logger.debug(`Найдено ${tableColumns.length} колонок для синхронизации с чекбоксами`);

        let updatedCount = 0;

        tableColumns.forEach(tableColumn => {
            const def = tableColumn.getDefinition();
            if (!def.field) {
                this.logger.debug('Колонка без field пропущена');
                return;
            }

            const fieldName = def.field.replace(/\./g, '_');
            const checkbox = columnList.querySelector(`#check_${fieldName}`);

            if (checkbox) {
                const isVisible = tableColumn.isVisible();
                const oldState = checkbox.checked;
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
    },
};
