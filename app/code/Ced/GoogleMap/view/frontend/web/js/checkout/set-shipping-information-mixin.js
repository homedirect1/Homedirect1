define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setShippingInformationAction) {
        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            console.log('shippingAddress.customAttributes');
            console.log(shippingAddress.customAttributes);
            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {};
            }

            //shippingAddress['extension_attributes']['custom_field'] = shippingAddress.customAttributes['custom_field'];
            // pass execution to original action ('Magento_Checkout/js/action/set-shipping-information')

            if (shippingAddress.customAttributes !== undefined) {
                $.each(shippingAddress.customAttributes , function( key, value ) {
                    console.log('shippingAddress.key');
                    console.log(key);
                    console.log(value);
                    if (key == "latitude" || key == "longitude") {
                        if ($.isPlainObject(value)) {
                            value = value['value'];
                        }
                        shippingAddress['customAttributes'][key] = value;
                        shippingAddress['extension_attributes'][key] = value;
                    }
                });
            }

            return originalAction();
        });
    };
});
