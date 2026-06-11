define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract',
    'jquery',
    'SystemCode_CustomerAddressAutocompleteBrazil/js/mask-helper',
    'SystemCode_CustomerAddressAutocompleteBrazil/js/street-line-mapping',
    'mage/url',
    'loader'
], function (_, registry, Abstract, $, maskHelper, streetLineMapping, urlBuilder, loader) {
    'use strict';

    return Abstract.extend({
        defaults: {
            imports: {
                update: '${ $.parentName }.country_id:value'
            }
        },

        initialize: function () {
            this._super();
            this.fieldsUnlocked = false;
            this.postcodeMaskApplied = false;
            this.lastLookupZip = '';
            this.lookupInProgress = false;
            this.disableLockGeneration = 0;

            _.defer(function () {
                this.initializeAddressFieldsState();
            }.bind(this));

            return this;
        },

        onElementRender: function (element) {
            if (this.postcodeMaskApplied) {
                return;
            }

            maskHelper.apply(element);
            this.postcodeMaskApplied = true;
        },

        getConfig: function () {
            return window.checkoutConfig.customerAddressAutocompleteBrazil || {};
        },

        getAddressComponentNames: function () {
            return ['street', 'street_prefix', 'city', 'region', 'region_id', 'country_id'];
        },

        getStreetLineNames: function () {
            return ['street.0', 'street.1', 'street.2', 'street.3'];
        },

        getAdditionalComponentNames: function () {
            return ['region_id_input'];
        },

        getAllComponentNames: function () {
            return this.getAddressComponentNames()
                .concat(this.getStreetLineNames())
                .concat(this.getAdditionalComponentNames());
        },

        forEachAddressComponent: function (callback) {
            var parentName = this.parentName;

            this.getAllComponentNames().forEach(function (fieldName) {
                var component = registry.get(parentName + '.' + fieldName);

                if (component) {
                    callback(component);
                }
            });
        },

        disableComponent: function (component) {
            if (component && typeof component.disable === 'function') {
                component.disable();
            }
        },

        enableComponent: function (component) {
            if (component && typeof component.enable === 'function') {
                component.enable();
            }
        },

        disableAddressFieldsWhenReady: function (attempts, delay) {
            if (this.fieldsUnlocked || !this.getConfig().lockFieldsUntilLookup) {
                return;
            }

            var self = this,
                generation = this.disableLockGeneration,
                remaining = attempts || 30,
                wait = delay || 100;

            function tryDisable() {
                if (generation !== self.disableLockGeneration ||
                    self.fieldsUnlocked ||
                    !self.getConfig().lockFieldsUntilLookup
                ) {
                    return;
                }

                self.forEachAddressComponent(function (component) {
                    self.disableComponent(component);
                });

                if (remaining > 0) {
                    remaining--;
                    window.setTimeout(tryDisable, wait);
                }
            }

            tryDisable();
        },

        unlockAddressFields: function () {
            this.disableLockGeneration++;
            this.fieldsUnlocked = true;
            this.enableAddressFields();
        },

        lockAddressFieldsUntilLookup: function () {
            if (!this.getConfig().lockFieldsUntilLookup) {
                return;
            }

            this.fieldsUnlocked = false;
            this.disableLockGeneration++;
            this.disableAddressFieldsWhenReady();
        },

        initializeAddressFieldsState: function () {
            if (!this.getConfig().lockFieldsUntilLookup) {
                return;
            }

            this.lockAddressFieldsUntilLookup();
        },

        disableAddressFields: function () {
            this.forEachAddressComponent(function (component) {
                this.disableComponent(component);
            }.bind(this));
        },

        enableAddressFields: function () {
            this.forEachAddressComponent(function (component) {
                this.enableComponent(component);
            }.bind(this));
        },

        lockComponentIfFilled: function (component, value) {
            if (!this.getConfig().lockFoundFields || !component || !value) {
                return;
            }

            this.disableComponent(component);
        },

        clearAddressFieldValues: function () {
            var parentName = this.parentName;

            this.getStreetLineNames().concat(['city']).forEach(function (fieldName) {
                var component = registry.get(parentName + '.' + fieldName);

                if (component && typeof component.value === 'function') {
                    component.value('');
                }
            });

            ['region_id', 'region', 'region_id_input', 'country_id', 'street_prefix'].forEach(function (fieldName) {
                var component = registry.get(parentName + '.' + fieldName);

                if (component && typeof component.value === 'function') {
                    component.value('');
                }
            });
        },

        resetAddressFields: function () {
            this.unlockAddressFields();
            this.clearAddressFieldValues();
        },

        update: function (value) {
            var country = registry.get(this.parentName + '.country_id'),
                options = country.indexedOptions,
                option;

            if (!value) {
                return;
            }

            if (options[value]) {
                option = options[value];

                if (option.is_zipcode_optional) {
                    this.error(false);
                    this.validation = _.omit(this.validation, 'required-entry');
                } else {
                    this.validation['required-entry'] = true;
                }

                this.required(!option.is_zipcode_optional);
            }
        },

        onUpdate: function () {
            this.bubble('update', this.hasChanged());

            var config = this.getConfig(),
                zipCode = (this.value() || '').replace(/\D/g, '');

            if (!config.enabled) {
                return;
            }

            if (zipCode.length !== 8) {
                if (zipCode.length < 8 && (this.fieldsUnlocked || this.lastLookupZip !== '')) {
                    this.lastLookupZip = '';
                    this.unlockAddressFields();
                    this.clearAddressFieldValues();
                }

                if (!this.fieldsUnlocked) {
                    this.lockAddressFieldsUntilLookup();
                }

                return;
            }

            if (this.lookupInProgress || zipCode === this.lastLookupZip) {
                return;
            }

            this.unlockAddressFields();
            this.clearAddressFieldValues();

            if (config.lockFieldsUntilLookup) {
                this.fieldsUnlocked = false;
                this.disableLockGeneration++;
                this.disableAddressFields();
            }

            this.lastLookupZip = zipCode;
            this.lookupInProgress = true;
            $('body').loader('show');

            var element = this,
                ajaxUrl = urlBuilder.build(config.searchUrlPath + zipCode);

            $.ajax({
                url: ajaxUrl,
                dataType: 'json',
                timeout: 5000
            }).done(function (data) {
                if (element.isLookupFailure(data)) {
                    element.handleApiFailure();
                } else {
                    element.fillAddressFields(data);
                }
            }).fail(function () {
                element.handleApiFailure();
            }).always(function () {
                element.lookupInProgress = false;
                $('body').loader('hide');
            });
        },

        isLookupFailure: function (data) {
            return !data || data.error || data.valid === false || data.is_valid === false;
        },

        handleApiFailure: function () {
            this.lastLookupZip = '';
            this.resetAddressFields();
        },

        fillStreetLines: function (data) {
            var parentName = this.parentName,
                config = this.getConfig();

            streetLineMapping.getMapping(config).forEach(function (fieldKey, lineIndex) {
                if (!fieldKey || fieldKey === 'none') {
                    return;
                }

                var component = registry.get(parentName + '.street.' + lineIndex),
                    value = streetLineMapping.getFieldValue(data, fieldKey);

                if (component) {
                    component.value(value);
                    this.lockComponentIfFilled(component, value);
                }
            }.bind(this));
        },

        fillAddressFields: function (data) {
            this.unlockAddressFields();

            var parentName = this.parentName,
                city = registry.get(parentName + '.city'),
                regionId = registry.get(parentName + '.region_id'),
                region = registry.get(parentName + '.region'),
                regionInput = registry.get(parentName + '.region_id_input'),
                countryId = registry.get(parentName + '.country_id'),
                streetPrefix = registry.get(parentName + '.street_prefix');

            this.fillStreetLines(data);

            if (city) {
                city.value(data.city || '');
                this.lockComponentIfFilled(city, data.city);
            }

            if (countryId) {
                countryId.value('BR');
                this.lockComponentIfFilled(countryId, 'BR');
            }

            if (regionId && data.region_id) {
                this.setRegionValue(regionId, data.region_id);
                this.lockComponentIfFilled(regionId, data.region_id);
                this.lockComponentIfFilled(region, data.region_id);
                this.lockComponentIfFilled(regionInput, data.region_id);
            }

            if (streetPrefix) {
                this.lockComponentIfFilled(streetPrefix, streetPrefix.value());
            }
        },

        setRegionValue: function (regionComponent, regionValue) {
            var normalizedValue = String(regionValue),
                applyValue = function () {
                    var option = regionComponent.getOption(normalizedValue) ||
                        regionComponent.getOption(parseInt(normalizedValue, 10));

                    if (!option) {
                        return false;
                    }

                    regionComponent.value(option.value);
                    regionComponent.error(false);

                    return true;
                };

            if (applyValue()) {
                return;
            }

            var optionsSubscription = regionComponent.options.subscribe(function () {
                if (applyValue()) {
                    optionsSubscription.dispose();
                }
            });
        }
    });
});
