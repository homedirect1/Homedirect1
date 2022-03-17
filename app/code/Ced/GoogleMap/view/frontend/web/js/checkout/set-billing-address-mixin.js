define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper,quote) {
    'use strict';

    return function (setBillingAddressAction) {
        return wrapper.wrap(setBillingAddressAction, function (originalAction, messageContainer) {

            let billingAddress = quote.billingAddress();

            console.log('billingAddress');
            console.log(billingAddress);

            if(billingAddress !== undefined) {
                if (billingAddress['extension_attributes'] === undefined) {
                    billingAddress['extension_attributes'] = {};
                }

                if (billingAddress.customAttributes !== undefined) {
                    console.log(billingAddress.customAttributes);
                    /*var addressType = '';
                    billingAddress.customAttributes.forEach(function (item) {
                        if(item.attribute_code === 'latitude'){
                            addressType = item.value;
                            billingAddress['extension_attributes']['latitude'] = addressType;
                        }
                        if(item.attribute_code === 'longitude'){
                            addressType = item.value;
                            billingAddress['extension_attributes']['longitude'] = addressType;
                        }
                    });  */

                    $.each(billingAddress.customAttributes , function( key, value ) {
                        console.log('billingAddress.key');
                        console.log(key);
                        console.log(value);
                        if ($.isPlainObject(value)) {
                            value = value['value'];
                            key = value['attribute_code'];
                        }

                        if (key == "latitude" || key == "longitude") {
                            billingAddress['customAttributes'][key] = value;
                            billingAddress['extension_attributes'][key] = value;
                        }
                    });
                }
            }
            return originalAction(messageContainer);
        });
    };
});
