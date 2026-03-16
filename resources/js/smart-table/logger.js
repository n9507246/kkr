export const logger = {
    config: {
        enabled: false,
        prefix: '',
        showTimestamp: true,
        showLevel: true,
        levels: {
            log: true,
            info: true,
            warn: true,
            error: true,
            debug: true,
        },
        timestampFormat: {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            fractionalSecondDigits: 3,
        },
    },

    configure: function(options = {}) {
        this.config = this.mergeDeep(this.config, options);
        return this;
    },

    createInstance: function(options = {}) {
        const instance = Object.create(this);
        instance.config = this.mergeDeep({}, this.config);
        instance.configure(options);
        return instance;
    },

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

    formatMessage: function(level, message) {
        const parts = [];

        if (this.config.prefix) {
            parts.push(this.config.prefix);
        }

        if (this.config.showTimestamp) {
            const timestamp = new Date().toLocaleTimeString('ru-RU', this.config.timestampFormat);
            parts.push(`[${timestamp}]`);
        }

        if (this.config.showLevel) {
            parts.push(`[${level.toUpperCase()}]`);
        }

        parts.push(message);

        return parts.join(' ');
    },

    shouldLog: function(level) {
        return this.config.enabled && this.config.levels[level] === true;
    },

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

    logLevel: function(level, message) {
        if (this.shouldLog(level)) {
            const method = console[level] || console.log;
            method.call(console, this.formatMessage(level, message));
        }
    },
};
