export const excelExporter = {
    table: null,
    logger: null,
    ajaxURL: null,
    exportButtonId: null,
    extraExportColumns: [],
    treeExport: {
        enabled: true,
        childField: 'dopolnitelnie_obekti',
        labelField: 'name',
        childPrefix: '↳ ',
        collapseChildrenByDefault: true,
        childRowFillColor: 'E7F1FF',
    },

    init(table, options = {}) {
        this.table = table;
        this.logger = options.logger || null;
        this.ajaxURL = options.ajaxURL || null;
        this.extraExportColumns = Array.isArray(options.extraExportColumns) ? options.extraExportColumns : [];
        this.treeExport = {
            ...this.treeExport,
            ...(options.treeExport || {}),
        };

        const tableId = this.table?.element?.id;
        this.exportButtonId = options.exportButtonId || `export-excel-btn-${tableId}`;

        const btn = document.getElementById(this.exportButtonId)
            || document.querySelector(
                `[to-smart-table="${tableId}"][role="export_excel"], ` +
                `[to-smart-table="${tableId}"][role="export-to-excel"]`
            );

        if (!btn) {
            this.logger?.warn(
                `Excel export button not found for table "${tableId}". ` +
                `Expected id "${this.exportButtonId}" or [to-smart-table="${tableId}"][role="export_excel"]`
            );
            return;
        }

        btn.addEventListener('click', () => this.export());
        this.logger?.info('Excel exporter initialized');
    },

    async export() {
        try {
            this.logger?.info('Starting Excel export');
            this.logger?.debug?.(`[EXPORT] ajaxURL=${this.ajaxURL || 'local-data'}`);
            this.logger?.debug?.(`[EXPORT] extraExportColumns=${JSON.stringify(this.extraExportColumns.map(c => ({ title: c.title, field: c.field })))} `);

            const baseColumns = this.table.getColumnLayout()
                .filter(col => col.visible !== false && col.field && col.field !== 'actions');
            const extraColumns = this.extraExportColumns.filter(col => col && col.field);
            const columns = [...baseColumns, ...extraColumns];
            this.logger?.info(`[EXPORT] baseColumns=${baseColumns.length}, extraColumns=${extraColumns.length}, totalColumns=${columns.length}`);
            this.logger?.debug?.(`[EXPORT] columns=${JSON.stringify(columns.map(c => ({ title: c.title, field: c.field, hasFormatter: typeof c.exportFormatter === 'function' })))} `);

            const data = await this.fetchAllData();
            this.logger?.info(`[EXPORT] rows fetched=${Array.isArray(data) ? data.length : 0}`);
            if (Array.isArray(data) && data.length > 0) {
                this.logger?.debug?.(`[EXPORT] first row keys=${Object.keys(data[0]).join(', ')}`);
            }

            const exportData = this.treeExport.enabled
                ? this.groupTreeRowsForExport(data)
                : data;

            this.logger?.info(`[EXPORT] rows prepared for worksheet=${Array.isArray(exportData) ? exportData.length : 0}`);
            this.buildWorkbook(columns, exportData);
            this.logger?.info('[EXPORT] workbook build completed');

        } catch (e) {
            this.logger?.error('Export error: ' + e.message);
            this.logger?.error('[EXPORT] stack: ' + (e?.stack || 'no-stack'));
        }
    },

    async fetchAllData() {
        this.logger?.debug?.('[EXPORT] fetchAllData started');
        if (!this.ajaxURL) {
            this.logger?.debug?.('[EXPORT] no ajaxURL, using table.getData()');
            return this.table.getData();
        }

        const params = new URLSearchParams();
        let currentParams = {};

        // 1. Извлекаем динамические параметры (фильтры) из настроек таблицы
        // В твоем логе это функция, которая собирает FormData из filterForm
        if (typeof this.table.options.ajaxParams === "function") {
            currentParams = this.table.options.ajaxParams();
            this.logger?.debug?.('[EXPORT] ajaxParams loaded from function');
        } else if (this.table.options.ajaxParams) {
            currentParams = this.table.options.ajaxParams;
            this.logger?.debug?.('[EXPORT] ajaxParams loaded from object');
        }

        // 2. Добавляем фильтры в URL
        // Если твоя функция возвращает { filters: { field: value } }, обрабатываем это
        if (currentParams.filters) {
            this.logger?.debug?.(`[EXPORT] filters count=${Object.keys(currentParams.filters).length}`);
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

        // 3. Добавляем текущую сортировку таблицы в формате backend: sort[index][field|dir]
        const sorters = typeof this.table.getSorters === 'function' ? this.table.getSorters() : [];
        this.logger?.debug?.(`[EXPORT] sorters count=${Array.isArray(sorters) ? sorters.length : 0}`);
        if (Array.isArray(sorters) && sorters.length > 0) {
            sorters.forEach((sorter, index) => {
                if (!sorter?.field) {
                    return;
                }

                params.append(`sort[${index}][field]`, sorter.field);
                params.append(`sort[${index}][dir]`, sorter.dir || 'asc');
            });
        }

        // 4. Добавляем параметры пагинации для выгрузки "всего"
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
        this.logger?.info(`[EXPORT] fetch status=${response.status}`);

        if (!response.ok) {
            throw new Error(`Ошибка сервера: ${response.status}`);
        }

        const json = await response.json();
        this.logger?.debug?.(`[EXPORT] response keys=${Object.keys(json || {}).join(', ')}`);
        
        // Возвращаем данные (учитывая структуру ответа Laravel/Tabulator)
        return json.data || [];
    },
    buildWorkbook(columns, data) {
        this.logger?.debug?.(`[EXPORT] buildWorkbook start: columns=${columns.length}, rows=${Array.isArray(data) ? data.length : 0}`);

        const header = columns.map(c => c.title || c.field);
        const rows = data.map(row =>
            columns.map(c => {
                const rawValue = this.getNestedValue(row, c.field);
                const value = typeof c.exportFormatter === 'function'
                    ? c.exportFormatter(rawValue, row)
                    : rawValue;

                let exportValue = value;

                if (
                    row?.__export_row_type === 'child' &&
                    this.treeExport?.enabled &&
                    c.field === this.treeExport.labelField
                ) {
                    const prefix = this.treeExport.childPrefix ?? '↳ ';
                    exportValue = `${prefix}${value ?? ''}`;
                }

                return this.formatCellValueForExport(exportValue);
            })
        );

        const worksheetData = [header, ...rows];
        this.logger?.debug?.(`[EXPORT] worksheetData rows=${worksheetData.length}`);

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(worksheetData);

        if (this.treeExport?.enabled) {
            this.applyChildRowsColor(ws, data, columns.length);
        }

        if (this.treeExport?.enabled) {
            ws['!rows'] = this.buildOutlineRowsMeta(data);
        }

        ws['!cols'] = this.calculateWidths(columns, data);
        this.logger?.debug?.(`[EXPORT] calculated widths for ${ws['!cols']?.length || 0} columns`);

        XLSX.utils.book_append_sheet(wb, ws, 'Sheet1');
        XLSX.writeFile(wb, 'export.xlsx');

        this.logger?.info('Excel file saved');
    },

    calculateWidths(columns, data) {
        return columns.map(col => {

            let max = this.visualLength(col.title || '');

            data.forEach(row => {
                const rawValue = this.getNestedValue(row, col.field);
                const preparedValue = typeof col.exportFormatter === 'function'
                    ? col.exportFormatter(rawValue, row)
                    : rawValue;
                const value = this.formatCellValueForExport(preparedValue);
                if (value !== null && value !== undefined) {
                    const len = this.visualLength(String(value));
                    if (len > max) max = len;
                }
            });

            return { wch: Math.min(Math.max(max + 2, 10), 80) };
        });
    },

    visualLength(text) {
        let length = 0;

        for (let char of text) {
            if (/[А-Яа-яЁё]/.test(char)) length += 1;
            else if (/[A-Z]/.test(char)) length += 1.1;
            else length += 1;
        }

        return Math.ceil(length);
    },

    getNestedValue(obj, path) {
        return path.split('.').reduce((acc, key) => acc ? acc[key] : null, obj);
    },

    formatCellValueForExport(value) {
        if (value === null || value === undefined || value === '') {
            return value;
        }

        if (Array.isArray(value)) {
            return value.map(item => this.formatCellValueForExport(item)).join(', ');
        }

        if (typeof value === 'object') {
            return JSON.stringify(value);
        }

        if (typeof value !== 'string') {
            return value;
        }

        const dateOnlyMatch = value.match(/^(\d{4})-(\d{2})-(\d{2})$/);
        if (dateOnlyMatch) {
            return `${dateOnlyMatch[3]}-${dateOnlyMatch[2]}-${dateOnlyMatch[1]}`;
        }

        const isoLikeMatch = value.match(/^(\d{4})-(\d{2})-(\d{2})[T\s]/);
        if (isoLikeMatch) {
            return `${isoLikeMatch[3]}-${isoLikeMatch[2]}-${isoLikeMatch[1]}`;
        }

        return value;
    },

    groupTreeRowsForExport(data) {
        const childField = this.treeExport.childField || '_children';

        if (!Array.isArray(data)) {
            return [];
        }

        const grouped = [];
        const walk = (row, level = 0, parentId = null) => {
            const children = Array.isArray(row?.[childField]) ? row[childField] : [];

            grouped.push({
                ...row,
                __export_row_type: level === 0 ? 'parent' : 'child',
                __export_parent_id: parentId,
                __export_level: level,
            });

            children.forEach((childRow) => walk(childRow, level + 1, row?.id ?? null));
        };

        data.forEach((rootRow) => walk(rootRow, 0, null));

        return grouped;
    },

    buildOutlineRowsMeta(data) {
        const collapseChildren = !!this.treeExport?.collapseChildrenByDefault;
        const rowsMeta = [{}]; // Header row

        data.forEach((row) => {
            const rawLevel = Number(row?.__export_level ?? 0);
            const level = Number.isFinite(rawLevel) ? Math.max(0, Math.min(rawLevel, 7)) : 0;

            rowsMeta.push({
                level,
                hidden: collapseChildren && level > 0,
            });
        });

        return rowsMeta;
    },

    applyChildRowsColor(ws, data, columnCount) {
        const fillColor = this.treeExport?.childRowFillColor || 'E7F1FF';

        if (!Array.isArray(data) || !columnCount) {
            return;
        }

        data.forEach((row, index) => {
            if (row?.__export_row_type !== 'child') {
                return;
            }

            const worksheetRowIndex = index + 1; // +1 because row 0 is header

            for (let colIndex = 0; colIndex < columnCount; colIndex++) {
                const cellAddress = XLSX.utils.encode_cell({ r: worksheetRowIndex, c: colIndex });
                const cell = ws[cellAddress];
                if (!cell) continue;

                cell.s = cell.s || {};
                cell.s.fill = {
                    patternType: 'solid',
                    fgColor: { rgb: fillColor },
                    bgColor: { rgb: fillColor },
                };
            }
        });
    },

};
