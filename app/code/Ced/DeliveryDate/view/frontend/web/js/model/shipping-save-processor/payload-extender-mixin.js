define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote) {
    'use strict';

    return function (target) {
        return wrapper.wrap(target, function (object, payload) {
            object(payload);

            var deliveryDate = $('[name="delivery_date"]').val();
            var timestamp = $('[name="timestamp"]').val();
            var comment = $('[name="delivery_comment"]').val();

            if(!payload.addressInformation['extension_attributes']) {
                payload.addressInformation['extension_attributes'] = {};
            }
            payload.addressInformation['extension_attributes'] = {
                delivery_date: deliveryDate ,
                timestamp: timestamp,
                delivery_comment: comment
            };
            return payload;
        });
    };
});
