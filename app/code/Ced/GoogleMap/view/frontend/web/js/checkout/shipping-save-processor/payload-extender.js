define([
    'jquery',
    'mage/utils/wrapper'
], function (
    jQuery,
    wrapper
) {
    'use strict';

    return function (processor) {
        return wrapper.wrap(processor, function (proceed, payload) {
            payload = proceed(payload);

            let shippingAddress =  payload.addressInformation.shipping_address;
            let latitude = jQuery('[name="custom_attributes[latitude]"]').val();
            let longitude = jQuery('[name="custom_attributes[longitude]"]').val();

            if (latitude === "" || latitude == null){
                latitude = null;
                if (shippingAddress.customAttributes !== "undefined" && shippingAddress.customAttributes !== null &&
                    (shippingAddress.customAttributes.latitude !== "undefined" ||
                        shippingAddress.customAttributes.latitude !== null)) {
                    latitude = shippingAddress.customAttributes.latitude.value;
                }
            }

            if(longitude === "" || longitude == null){
                longitude = null;
                if (shippingAddress.customAttributes !== "undefined" && shippingAddress.customAttributes !== null &&
                    (shippingAddress.customAttributes.longitude !== "undefined" ||
                        shippingAddress.customAttributes.longitude !== null)) {
                    longitude = shippingAddress.customAttributes.longitude.value;
                }
            }

            let goneExtentionAttributes = {
                'latitude': latitude,
                'longitude': longitude
            };
            payload.addressInformation.extension_attributes = _.extend(
                payload.addressInformation.extension_attributes,
                goneExtentionAttributes
            );

            return payload;
        });
    };
});
