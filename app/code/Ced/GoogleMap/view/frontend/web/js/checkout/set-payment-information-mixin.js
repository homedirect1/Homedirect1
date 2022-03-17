define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (placeOrderAction) {

        return wrapper.wrap(placeOrderAction, function (originalAction) {
            let billingAddress = quote.billingAddress();
            let shippingAddress = quote.shippingAddress();
            if (billingAddress['extension_attributes'] === undefined) {
                billingAddress['extension_attributes'] = {};
            }

            if (shippingAddress.customAttributes !== undefined) {
                var addressType = '';  
                shippingAddress.customAttributes.forEach(function (item) {
                    if(item.attribute_code === 'latitude'){
                        addressType = item.value;
                        shippingAddress['extension_attributes']['latitude'] = addressType;
                    }
                    if(item.attribute_code === 'longitude'){
                        addressType = item.value;
                        shippingAddress['extension_attributes']['longitude'] = addressType;
                    }
                 });  
            }

            return originalAction();
        });
    };
});
