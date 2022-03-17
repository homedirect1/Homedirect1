/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        'Ced_Perkmshipping/js/model/shipping-rates-validator/distance',
        'Ced_Perkmshipping/js/model/shipping-rates-validation-rules/distance'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        distanceShippingRatesValidator,
        distanceShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('perkmshipping', distanceShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('perkmshipping', distanceShippingRatesValidationRules);
        return Component;
    }
);
