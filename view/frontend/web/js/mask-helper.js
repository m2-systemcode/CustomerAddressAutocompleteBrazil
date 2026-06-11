define([
    'jquery',
    'SystemCode_Customer/js/jquery.mask'
], function ($) {
    'use strict';

    var maskOptions = {
            clearIfNotMatch: true
        },
        cepMask = '00000-000';

    function resolve$element(target) {
        if (!target) {
            return $();
        }

        if (target instanceof $) {
            return target;
        }

        if (target.nodeType === 1) {
            return $(target);
        }

        return $(target);
    }

    function applyMask($element) {
        if (!$element.length || typeof $.fn.mask !== 'function') {
            return false;
        }

        $element.unmask();
        $element.mask(cepMask, maskOptions);

        return true;
    }

    return {
        apply: function (target) {
            return applyMask(resolve$element(target));
        }
    };
});
