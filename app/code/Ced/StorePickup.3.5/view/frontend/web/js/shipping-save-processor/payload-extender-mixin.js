define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote',
], function ($, wrapper, quote) {
    'use strict';

    return function (target) {
        return wrapper.wrap(target, function (object, payload) {
            object(payload);
            let vendorId;
            let storepickup = quote.shippingMethod().method_code;
            let newData = storepickup.split(':');
            let storeData = '';
            for(let i=0; i<newData.length; i++){
                if (newData[i].indexOf("storepickupshipping") >= 0){
                    let inputShipping = newData[i].split('~');
                    if(inputShipping.length>1){
                        vendorId = inputShipping[1];
                    }else{
                        vendorId = 0;
                    }
                    let storeId = $("#ced_stores_list_"+vendorId).val();
                    let datField = $("#calendar_inputField_"+vendorId).val();
                    storeData += vendorId+':'+storeId+':'+datField+'#';
                }
            }

            var extensionAttributes = {};
            if(!payload.addressInformation['extension_attributes']) {
                payload.addressInformation['extension_attributes'] = {};
            }else{
                extensionAttributes = payload.addressInformation['extension_attributes'];
            }
            if (storeData) {
                $.extend(extensionAttributes, {store_pickup_data: storeData});
            } else {
                $.extend(extensionAttributes, {store_pickup_data: ''});
            }
            payload.addressInformation['extension_attributes'] = extensionAttributes;

            return payload;
        });
    };
});
