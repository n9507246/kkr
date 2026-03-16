import * as bootstrap from "bootstrap";
import { TabulatorFull as Tabulator } from "tabulator-tables";
import * as luxon from "luxon";
import * as XLSX from "xlsx-js-style";

import { logger } from "./smart-table/logger";
import { columnVisibilityController } from "./smart-table/columnVisibilityController";
import { excelExporter } from "./smart-table/excelExporter";
import { init_filter_panel_state } from "./smart-table/filterPanelState";

window.bootstrap = bootstrap;
window.Tabulator = Tabulator;
window.luxon = luxon;
window.XLSX = XLSX;

export { init_filter_panel_state };

export function create_smart_table(properties) {
    
    properties.debug = true
    const isDebug = !!properties.debug;
    console.log(isDebug)
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

    const escapedId = typeof CSS !== 'undefined' && typeof CSS.escape === 'function'
        ? CSS.escape(properties.id)
        : properties.id.replace(/([ #;?%&,.+*~\\':"!^$[\]()=>|/@])/g, '\\$1');
    const tableElements = document.querySelectorAll(`#${escapedId}`);

    if (tableElements.length === 0) {
        mainLogger.warn(`Таблица не будет создана так как не найден элемент с id="${properties.id}" в котором должна быть таблица`);
        return false;
    }

    if (tableElements.length > 1) {
        mainLogger.error(
            `Таблица не будет создана: найдено ${tableElements.length} элементов с одинаковым id="${properties.id}". ` +
            'id должен быть уникальным.'
        );
        return false;
    }

    mainLogger.debug(`Элемент с id="${properties.id}" найден`);
    const storageKeyBase = properties.storageKey || properties.id;
    mainLogger.debug(`Базовый ключ localStorage: ${storageKeyBase}`);
    let latestAjaxMeta = null;

    const shouldPersistFilterPanelState =
        properties.persist_filter_panel_state ?? properties.apply_filters ?? false;

    if (shouldPersistFilterPanelState) {
        init_filter_panel_state({
            tableId: properties.id,
            panelId: properties.filter_panel_id,
            storageKey: properties.filter_panel_storage_key,
            defaultOpen: properties.filter_panel_default_open ?? true,
        });
    }

    const tableConfig = {}
    tableConfig.height = properties.height ?? '400px',          // Высота таблицы
    tableConfig.layout = "fitColumns",                          // Автоматическое распределение колонок

    tableConfig.pagination = true,                              // Включаем пагинацию
    tableConfig.paginationMode = "remote"                       // Серверная пагинация
    tableConfig.paginationSize = 20                             // Записей на странице
    tableConfig.paginationSizeSelector = [10, 20, 50, 100]      // Возможные варианты записей на странице
    tableConfig.paginationCounter =
        properties.pagination_counter ?? (properties.extended_pagination_summary ? false : "rows")
    tableConfig.sortMode = "remote"                             // Серверная сортировка
    tableConfig.filterMode = "remote"                           // Серверная фильтрация
    
    tableConfig.dependencies = properties.dependencies

    //---------- DATA TREE НАСТРОЙКИ -------------
    if (properties.dataTree !== undefined) tableConfig.dataTree = properties.dataTree;
    if (properties.dataTreeChildField !== undefined) tableConfig.dataTreeChildField = properties.dataTreeChildField;
    if (properties.dataTreeStartExpanded !== undefined) tableConfig.dataTreeStartExpanded = properties.dataTreeStartExpanded;
    if (properties.dataTreeFilter !== undefined) tableConfig.dataTreeFilter = properties.dataTreeFilter;
    if (properties.dataTreeSort !== undefined) tableConfig.dataTreeSort = properties.dataTreeSort;
    if (properties.dataTreeExpandElement !== undefined) tableConfig.dataTreeExpandElement = properties.dataTreeExpandElement;
    if (properties.dataTreeCollapseElement !== undefined) tableConfig.dataTreeCollapseElement = properties.dataTreeCollapseElement;
    if (properties.dataTreeElementColumn !== undefined) tableConfig.dataTreeElementColumn = properties.dataTreeElementColumn;
    if (properties.dataTreeBranchElement !== undefined) tableConfig.dataTreeBranchElement = properties.dataTreeBranchElement;
    if (properties.dataTreeChildIndent !== undefined) tableConfig.dataTreeChildIndent = properties.dataTreeChildIndent;
    if (properties.dataTreeSelectPropagate !== undefined) tableConfig.dataTreeSelectPropagate = properties.dataTreeSelectPropagate;
    //---------------------------------------------

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
            latestAjaxMeta = {
                total: response.total ?? null,
                totalMain: response.total_main ?? null,
                totalChild: response.total_child ?? null,
            };

            // Возвращаем данные и информацию о последней странице
            const lastRow = response.last_row ?? response.total ?? null;
            return { 
                data: response.data, 
                last_page: response.last_page,
                ...(lastRow !== null ? { last_row: lastRow } : {})
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
    const columnsStorageKey = `tabulator-${storageKeyBase}-columns`;
    const visiableStateColumns = JSON.parse(localStorage.getItem(columnsStorageKey) || "{}");
    mainLogger.debug(`Загружено состояние видимости колонок: ${JSON.stringify(visiableStateColumns)}`);


    // Формируем колонки с указанием дополнительных параметров
    if (properties.columns) { 
        columnsList = properties.columns.map(col => {
            const resolvedVisible = visiableStateColumns[col.field] !== undefined
                ? visiableStateColumns[col.field]
                : (col.visible ?? true);



            return {
                // Минимальная ширина колонки по умолчанию
                minWidth: 120,

                // Остальные параметры из переданной конфигурации колонки
                ...col,

                // Устанавливаем видимость колонки (информация из localStorage)
                // Если в localStorage есть значение - используем его,
                // иначе берем настройку колонки, а затем true по умолчанию
                visible: resolvedVisible,
            };
        });
        
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

    // Пользовательские обработчики Tabulator
    if (typeof properties.rowClick === "function") {
        tableConfig.rowClick = properties.rowClick;
    }
    if (typeof properties.rowFormatter === "function") {
        tableConfig.rowFormatter = properties.rowFormatter;
    }
    
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

    if(properties.apply_filters) {
        // Находим форму фильтров
        const filterForm = document.querySelector(
            `[to-smart-table="${properties.id}"][role="fiters_table"], ` +
            `[to-smart-table="${properties.id}"][role="filters_table"]`
        );
        
        if (!filterForm) {
            mainLogger.warn(
                'Форма фильтров не найдена. ' +
                `Убедитесь, что в HTML присутствует элемент с атрибутами: ` +
                `to-smart-table="${properties.id}" и role="fiters_table" (или role="filters_table")`
            );
        } else {
            mainLogger.debug('Форма фильтров найдена');

            // Ключ для хранения фильтров в localStorage
            const storageKey = `tabulator-${storageKeyBase}-filters`;
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
    }
    // -----------------------------------------------------------------------------

    mainLogger.debug('Создание экземпляра таблицы Tabulator');
    
    // Создаем экземпляр таблицы Tabulator
    const table = new Tabulator(`#${properties.id}`, tableConfig);
    const paginationSummaryElement = properties.extended_pagination_summary
        ? (
            document.querySelector(
                `[to-smart-table="${properties.id}"][role="extended_pagination_summary"]`
            ) || document.createElement('div')
        )
        : null;

    if (paginationSummaryElement && !paginationSummaryElement.isConnected) {
        paginationSummaryElement.className =
            'small mt-2 text-body-secondary d-flex justify-content-between gap-3';
        table.element.insertAdjacentElement('beforebegin', paginationSummaryElement);
    }

    mainLogger.debug('Таблица Tabulator создана');

    const updatePaginationSummary = () => {
        if (!paginationSummaryElement) {
            return;
        }

        if (!latestAjaxMeta?.total) {
            paginationSummaryElement.textContent = '';
            return;
        }

        const page = Number(table.getPage() || 1);
        const pageSize = Number(table.getPageSize() || latestAjaxMeta.total);
        const currentRowCount = table.getDataCount("active");

        if (!currentRowCount) {
            paginationSummaryElement.textContent = '';
            return;
        }

        const startRow = ((page - 1) * pageSize) + 1;
        const endRow = startRow + currentRowCount - 1;
        const totalMain = latestAjaxMeta.totalMain ?? 0;
        const totalChild = latestAjaxMeta.totalChild ?? 0;

        paginationSummaryElement.textContent =
            `Показано ${startRow}-${endRow} из ${latestAjaxMeta.total} объектов ` +
            `(${totalMain} основных • ${totalChild} доп)`;
    };

    if (paginationSummaryElement) {
        table.on('dataProcessed', updatePaginationSummary);
        table.on('pageLoaded', updatePaginationSummary);
    }

    // Запускаем управление видимостью колонок, если это указано в параметрах
    const isColumnVisibilityEnabled =
        properties.control_column_visibility ?? properties.controll_column_visiable;

    if (isColumnVisibilityEnabled) {
        mainLogger.debug('Инициализация управления видимостью колонок');
        // Передаем основной логгер в управление видимостью
        columnVisibilityController.init(table, {
            debug: properties.debug,
            logger: mainLogger,
            storageKeyBase: storageKeyBase,
        });
    }

    // Запускаем экспорт в Excel, если это указано в параметрах
    if  (properties.export_to_excel) {
        const treeExportOptions = properties.treeExport || {};
        const defaultTreeLabelField =
            properties.columns?.find(col => col?.field)?.field || 'name';

        const resolvedTreeExport = {
            enabled: treeExportOptions.enabled ?? true,
            childField: treeExportOptions.childField ?? properties.dataTreeChildField ?? 'dopolnitelnie_obekti',
            labelField: treeExportOptions.labelField ?? defaultTreeLabelField,
            childPrefix: treeExportOptions.childPrefix ?? '↳ ',
            collapseChildrenByDefault: treeExportOptions.collapseChildrenByDefault ?? true,
        };


        excelExporter.init(table, {
            logger: mainLogger,
            ajaxURL: properties.ajaxURL || null,
            exportButtonId: properties.exportButtonId,
            extraExportColumns: properties.extraExportColumns || [],
            treeExport: resolvedTreeExport
        });
    }
    
    mainLogger.info(`Таблица с id="${properties.id}" успешно создана`);
    
    // Возвращаем экземпляр таблицы
    return table;
}
