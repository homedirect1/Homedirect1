
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

/*global define,alert*/
define(
        [
	    'jquery',
            'ko',
            'Magento_Checkout/js/model/quote',
            'Magento_Checkout/js/model/resource-url-manager',
            'mage/storage',
            'Magento_Checkout/js/model/payment-service',
            'Magento_Checkout/js/model/payment/method-converter',
            'Magento_Checkout/js/model/error-processor',
            'Magento_Checkout/js/model/full-screen-loader',
            'Magento_Checkout/js/action/select-billing-address'
        ],
        function (
		$,
                ko,
                quote,
                resourceUrlManager,
                storage,
                paymentService,
                methodConverter,
                errorProcessor,
                fullScreenLoader,
                selectBillingAddressAction
                ) {
            'use strict';

            return {
                saveShippingInformation: function () {
                    var payload;
                    var method = quote.shippingMethod().method_code;
                    var storepickup = method;
                    var newdata = storepickup.split(':');
                    var storedata='';
                    for(var i=0;i<newdata.length;i++){
                    	if (newdata[i].indexOf("storepickupshipping") >= 0){
                    		var inputshippping = newdata[i].split('~');
                    		if(inputshippping.length>1){
                        		var vendorId = inputshippping[1];
                        	}else{
                        		var vendorId = 0;
                        	}
                    		var storeid = $("#ced_stores_list_"+vendorId).val();
                    		var datfield = $("#calendar_inputField_"+vendorId).val();
                    		storedata+=vendorId+':'+storeid+':'+datfield+'#';
                    	}
                    	
                    }
                    if (storedata) {
                            payload = {
                                addressInformation: {
                                    shipping_address: quote.shippingAddress(),
                                    billing_address: quote.billingAddress(),
                                    shipping_method_code: quote.shippingMethod().method_code,
                                    shipping_carrier_code: quote.shippingMethod().carrier_code,
                                    extension_attributes: {
                                        csstore_pickup_data: storedata,
                                       
                                    }
                                }
                            };

                    }else{

                        if (!quote.billingAddress()) {
                            selectBillingAddressAction(quote.shippingAddress());
                        }
                        
                            payload = {
                            addressInformation: {
                                shipping_address: quote.shippingAddress(),
                                billing_address: quote.billingAddress(),
                                shipping_method_code: quote.shippingMethod().method_code,
                                shipping_carrier_code: quote.shippingMethod().carrier_code,
                                extension_attributes: {
                                    csstore_pickup_data: '', 
                                }
                            }
                        };
                    }
                    fullScreenLoader.startLoader();
                    return storage.post(
                        resourceUrlManager.getUrlForSetShippingInformation(quote),
                        JSON.stringify(payload)
                    ).done(
                        function (response) {
                            quote.setTotals(response.totals);
                            paymentService.setPaymentMethods(methodConverter(response.payment_methods));
                            fullScreenLoader.stopLoader();
                        }
                    ).fail(
                        function (response) {
                            errorProcessor.process(response);
                            fullScreenLoader.stopLoader();
                        }
                    );
                }
            };
        }
);
