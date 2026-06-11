define([], function () {
    'use strict';

    var defaultMapping = ['street', 'none', 'neighborhood', 'additional_info'];

    return {
        getMapping: function (config) {
            if (config && config.streetLineMapping && config.streetLineMapping.length) {
                return config.streetLineMapping;
            }

            return defaultMapping.slice();
        },

        getFieldValue: function (data, fieldKey) {
            if (!fieldKey || fieldKey === 'none') {
                return '';
            }

            return data[fieldKey] || '';
        }
    };
});
