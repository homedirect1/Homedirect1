var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-billing-address': {
                'Ced_GoogleMap/js/checkout/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'Ced_GoogleMap/js/checkout/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'Ced_GoogleMap/js/checkout/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'Ced_GoogleMap/js/checkout/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'Ced_GoogleMap/js/checkout/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-payment-information': {
                'Ced_GoogleMap/js/checkout/set-payment-information-mixin': true
            },
            /*'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Vendor_Module/js/model/shipping-save-processor/payload-extender': true
            }*/
        }
    }
};
