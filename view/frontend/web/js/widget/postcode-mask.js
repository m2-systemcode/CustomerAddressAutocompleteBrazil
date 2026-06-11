define([
    'jquery',
    'jquery/ui',
    'SystemCode_CustomerAddressAutocompleteBrazil/js/mask-helper'
], function ($, ui, maskHelper) {
    'use strict';

    $.widget('systemCode.postcodeMask', {
        _create: function () {
            maskHelper.apply(this.element);
        }
    });

    return $.systemCode.postcodeMask;
});
