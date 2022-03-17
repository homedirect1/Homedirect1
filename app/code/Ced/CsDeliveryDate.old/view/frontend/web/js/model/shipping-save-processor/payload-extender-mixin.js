define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote) {
    'use strict';
 
    return function (target) {
        return wrapper.wrap(target, function (object, payload) {
            object(payload);

            var extensionAttributes = {};
            if(!payload.addressInformation['extension_attributes']) {
                payload.addressInformation['extension_attributes'] = {};
            }else{
                extensionAttributes = payload.addressInformation['extension_attributes'];
            }

            var deliveryDate = [];
            var timestamp = [];
            var comment = [];
            var vendor = [];
            let multishippingEnable = window.checkoutConfig.shipping.multishippingEnable;
            let csDDmoduleEnable = window.checkoutConfig.shipping.csDDmoduleEnable;
            if(csDDmoduleEnable && multishippingEnable){
                var idCode =  quote.shippingMethod().method_code;
                var METHOD_SEPARATOR = ':';
                var SEPARATOR = '~';
                var methods = idCode.split(METHOD_SEPARATOR);
                for(var i = 0; i < methods.length; i ++){
                    var vendorId = (methods[i].split(SEPARATOR))[1];
                    if(vendorId === 'undefined'){
                        vendorId = '';
                    }
                    vendor[i] = vendorId;
                }
                deliveryDate = window.deliveryDate ;
                timestamp = window.timestamp ;
                comment = window.comment;

                deliveryDate = JSON.stringify(deliveryDate);
                timestamp = JSON.stringify(timestamp);
                comment = JSON.stringify(comment);
                vendor = JSON.stringify(vendor);

                $.extend(extensionAttributes, {
                        csdelivery_date: deliveryDate ,
                        cstimestamp: timestamp,
                        csvendorId: vendor,
                        csdelivery_comment: comment
                    });
            } else {
                $.extend(extensionAttributes, {
                    delivery_date: $('[name="delivery_date"]').val(),
                    timestamp: $('[name="timestamp"]').val(),
                    delivery_comment: $('[name="delivery_comment"]').val()
                });
            }
            payload.addressInformation['extension_attributes'] = extensionAttributes;

            return payload;
        });
    };
});
