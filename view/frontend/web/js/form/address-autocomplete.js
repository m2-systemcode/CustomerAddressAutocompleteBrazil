define([
    'jquery',
    'mage/url',
    'loader',
    'SystemCode_CustomerAddressAutocompleteBrazil/js/street-line-mapping'
], function ($, urlBuilder, loader, streetLineMapping) {
    'use strict';

    return function (config, element) {
        var moduleConfig = window.customerAddressAutocompleteBrazil || {},
            $form = $(element),
            $postcode = $form.find('#zip, #postcode').first(),
            addressInputSelector = [
                '#city',
                '#region_id',
                '#region',
                '#country',
                '#street_prefix',
                '[id^="street_"]',
                'input[name^="street["]'
            ].join(', '),
            fieldsUnlocked = false,
            lastLookupZip = '',
            lookupInProgress = false,
            disableLockGeneration = 0;

        if (!$postcode.length || !moduleConfig.enabled) {
            return;
        }

        function getAddressInputs() {
            return $form.find(addressInputSelector).filter(function () {
                return !$(this).is($postcode);
            });
        }

        function disableAddressFields() {
            getAddressInputs().prop('disabled', true);
        }

        function enableAddressFields() {
            getAddressInputs().each(function () {
                unlockField($(this));
            });
        }

        function disableAddressFieldsWhenReady(attempts, delay) {
            if (fieldsUnlocked || !moduleConfig.lockFieldsUntilLookup) {
                return;
            }

            var generation = disableLockGeneration,
                remaining = attempts || 20,
                wait = delay || 100;

            function tryDisable() {
                if (generation !== disableLockGeneration || fieldsUnlocked || !moduleConfig.lockFieldsUntilLookup) {
                    return;
                }

                disableAddressFields();

                if (remaining > 0) {
                    remaining--;
                    window.setTimeout(tryDisable, wait);
                }
            }

            tryDisable();
        }

        function unlockAddressFields() {
            disableLockGeneration++;
            fieldsUnlocked = true;
            enableAddressFields();
        }

        function lockAddressFieldsUntilLookup() {
            if (!moduleConfig.lockFieldsUntilLookup) {
                return;
            }

            fieldsUnlocked = false;
            disableLockGeneration++;
            disableAddressFieldsWhenReady();
        }

        function reorderPostcode() {
            if (!moduleConfig.postcodeBeforeAddress) {
                return;
            }

            var $zipField = $form.find('.field.zip').first(),
                $referenceField = $form.find('.field.street_prefix').first();

            if (!$referenceField.length) {
                $referenceField = $form.find('.field.street').first();
            }

            if ($zipField.length && $referenceField.length) {
                $zipField.insertBefore($referenceField);
            }
        }

        function unlockField($input) {
            $input.prop('readonly', false).prop('disabled', false).removeAttr('disabled');
            $input.siblings('input[type="hidden"][data-autocomplete-lock="1"]').remove();
        }

        function lockField($input, value) {
            if (!moduleConfig.lockFoundFields || !value) {
                return;
            }

            if ($input.is('select')) {
                if ($input.siblings('input[type="hidden"][data-autocomplete-lock="1"]').length) {
                    return;
                }

                $input.prop('disabled', true);
                $('<input>', {
                    type: 'hidden',
                    name: $input.attr('name'),
                    value: $input.val(),
                    'data-autocomplete-lock': '1'
                }).insertAfter($input);

                return;
            }

            $input.prop('readonly', true);
        }

        function getStreetLineLockItems(data) {
            return streetLineMapping.getMapping(moduleConfig).map(function (fieldKey, lineIndex) {
                return {
                    selector: '#street_' + (lineIndex + 1),
                    value: streetLineMapping.getFieldValue(data, fieldKey)
                };
            }).filter(function (item) {
                return item.value;
            });
        }

        function lockFilledFields(data) {
            var mapping = getStreetLineLockItems(data).concat([
                {selector: '#city', value: data.city},
                {selector: '#region_id', value: data.region_id},
                {selector: '#region', value: data.region_id},
                {selector: '#country', value: 'BR'},
                {selector: '#street_prefix', value: $form.find('#street_prefix').val()}
            ]);

            mapping.forEach(function (item) {
                var $input = $form.find(item.selector).first();

                if ($input.length) {
                    lockField($input, item.value);
                }
            });
        }

        function clearAddressFieldValues() {
            $form.find('[id^="street_"], input[name^="street["]').val('');
            $form.find('#city').val('');
            $form.find('#region_id').val('');
            $form.find('#region').val('');
            $form.find('#street_prefix').val('');
            $form.find('#country').val('');
        }

        function resetAddressFields() {
            unlockAddressFields();
            clearAddressFieldValues();
        }

        function fillStreetLines(data) {
            streetLineMapping.getMapping(moduleConfig).forEach(function (fieldKey, lineIndex) {
                if (!fieldKey || fieldKey === 'none') {
                    return;
                }

                $form.find('#street_' + (lineIndex + 1)).val(streetLineMapping.getFieldValue(data, fieldKey));
            });
        }

        function fillAddressFields(data) {
            unlockAddressFields();

            fillStreetLines(data);
            $form.find('#city').val(data.city || '');
            $form.find('#country').val('BR').trigger('change');

            if (data.region_id) {
                window.setTimeout(function () {
                    $form.find('#region_id').val(String(data.region_id)).trigger('change');
                    lockFilledFields(data);
                }, 0);

                return;
            }

            lockFilledFields(data);
        }

        function isLookupFailure(data) {
            return !data || data.error || data.valid === false || data.is_valid === false;
        }

        function handleApiFailure() {
            lastLookupZip = '';
            resetAddressFields();
        }

        reorderPostcode();

        if (moduleConfig.lockFieldsUntilLookup) {
            lockAddressFieldsUntilLookup();
        }

        function handlePostcodeInput() {
            var zipCode = $postcode.val().replace(/\D/g, '');

            if (zipCode.length !== 8) {
                if (zipCode.length < 8 && (fieldsUnlocked || lastLookupZip !== '')) {
                    lastLookupZip = '';
                    unlockAddressFields();
                    clearAddressFieldValues();
                }

                if (!fieldsUnlocked) {
                    lockAddressFieldsUntilLookup();
                }

                return;
            }

            if (lookupInProgress || zipCode === lastLookupZip) {
                return;
            }

            unlockAddressFields();
            clearAddressFieldValues();

            if (moduleConfig.lockFieldsUntilLookup) {
                fieldsUnlocked = false;
                disableLockGeneration++;
                disableAddressFields();
            }

            lastLookupZip = zipCode;
            lookupInProgress = true;
            $('body').loader('show');

            $.ajax({
                url: urlBuilder.build('rest/V1/systemcode-brazil-zipcode/search/' + zipCode),
                dataType: 'json',
                timeout: 5000
            }).done(function (data) {
                if (isLookupFailure(data)) {
                    handleApiFailure();
                } else {
                    fillAddressFields(data);
                }
            }).fail(function () {
                handleApiFailure();
            }).always(function () {
                lookupInProgress = false;
                $('body').loader('hide');
            });
        }

        $postcode.on('input', handlePostcodeInput);
    };
});
