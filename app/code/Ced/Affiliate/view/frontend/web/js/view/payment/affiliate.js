define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'storecredit',
                component: 'Ced_Affiliate/js/view/payment/affiliate/affiliatepayment'
            }
        );
        return Component.extend({});
    }
);