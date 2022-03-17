
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'Magento_Catalog/js/price-utils',
        'mage/url',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        priceUtils,
        url,
        $t
    ) {
        'use strict';

        return function (Shipping) {
            return Shipping.extend({
                defaults: {
                    template: 'Ced_CsMultiShipping/shipping'
                },

                initialize: function () {
                    let self = this;
                    this._super();

                    this.rates.subscribe(
                        function (grates) {
                            self.shippingRateGroups([]);
                            _.each(
                                grates, function (rate) {
                                    let carrierTitle = rate['carrier_title'];
                                    let carrier_code = rate['carrier_code'];
                                    if (self.shippingRateGroups.indexOf(carrierTitle) === -1
                                        && carrier_code !== 'vendor_rates') {
                                        self.shippingRateGroups.push(carrierTitle);
                                    }
                                }
                            );
                        }
                    );
                    return this;
                },

                initElement: function(element) {
                    if (element.index === 'shipping-address-fieldset') {
                        shippingRatesValidator.bindChangeHandlers(element.elems(), false);
                    }
                },

                getFormattedPrice: function (price) {
                    return priceUtils.formatPrice(price, quote.getPriceFormat());
                },

                getRatesForGroup: function (shippingRateGroupTitle) {
                    return _.filter(
                        this.rates(), function (rate) {
                            return shippingRateGroupTitle === rate['carrier_title'];
                        }
                    );
                },

                selectVirtualMethod: function(shippingMethod) {
                    let vendorId;
                    let inputid = shippingMethod.carrier_code+'_'+shippingMethod.method_code;
                    inputid = inputid.replace("~", "\\~");

                    let arr;
                    arr = inputid.split('~');
                    if(arr.length > 1){
                        vendorId = arr[1];
                    }else{
                        vendorId = 0;
                    }

                    let flag = true;
                    let METHOD_SEPARATOR = ':';
                    let SEPARATOR = '~';
                    let rates = [];
                    let sortedrate = [];
                    $('.vendor-rates').each(
                        function(index,elm){
                            flag = false;
                            $(elm).find('.radio').each(
                                function(i,input){
                                    if(input.checked) {
                                        flag = true;
                                        rates.push(input.value);
                                    }
                                }
                            );
                            if(!flag) {
                                flag = false;
                            }
                        }
                    );

                    if (inputid.indexOf("storepickupshipping") >= 0){
                        if(!$('#ced_stores_list_'+vendorId).is(':visible')){
                            let mapurl = url.build('csstorepickup/stores/getstores/vendor_id/'+vendorId);
                            $.ajax({
                                method: 'GET',
                                dataType: 'html',
                                url: mapurl,
                                showLoader: true,
                            }).success(function (result) {
                                $("#"+inputid).siblings('label').after(result);
                            });
                        }
                    }else{
                        if (vendorId) {}else{vendorId = 0;}
                        $('#ced_stores_list_'+vendorId).hide();
                        $("#store_view_map_"+vendorId).hide();
                        $("#oneValues_"+vendorId).hide();
                        $("#mapValues__"+vendorId).hide();
                    }
                    /**
                     * storepickup code
                     */

                    if(flag) {
                        for(let i = 0; i < rates.length; i ++){
                            let sortedValue = rates[i].split(SEPARATOR);
                            let pos = isNaN(parseInt(sortedValue[1])) ? 0 : parseInt(sortedValue[1]);
                            sortedrate[pos] = rates[i];
                        }
                        let rate = '';
                        for(let i=0;i< sortedrate.length;i++){
                            if(sortedrate[i]!==undefined) {
                                if(rate) {
                                    rate = rate + METHOD_SEPARATOR + sortedrate[i];
                                }else{
                                    rate =  sortedrate[i];
                                }
                            }
                        }
                        if(document.getElementById('s_method_vendor_rates_'+rate)) {
                            let event = new Event('click');
                            document.getElementById('s_method_vendor_rates_'+rate).dispatchEvent(event);
                        }
                    }
                    return true;
                },

                validateShippingInformation: function () {
                    /*latest changes by gulshan*/
                    let flag = true;
                    let rates = [];
                    $('.vendor-rates').each(
                        function(index,elm){
                            flag = false;
                            $(elm).find('.radio').each(
                                function(i,input){
                                    if(input.checked) {
                                        flag = true;
                                        rates.push(input.value);
                                    }
                                }
                            );
                            if(!flag) {
                                flag = false;
                            }
                        }
                    );
                    if(!flag) {
                        this.errorValidationMessage('Please select shipping method for each seller.');
                        return false;
                    }

                    let method = quote.shippingMethod().method_code;
                    let storepickup = method;
                    let newdata = storepickup.split(':');
                    let tmpstoredata='';
                    let tmphasStoredata= false;
                    let storeid = '';
                    let datfield = '';
                    let vendorId = 0;
                    for(let i=0;i<newdata.length;i++){
                        if (newdata[i].indexOf("storepickup") >= 0){
                            let inputshippping = newdata[i].split('~');
                            if(inputshippping.length>1){
                                vendorId = inputshippping[1];
                            }
                            storeid = $("#ced_stores_list_"+vendorId).val();
                            datfield = $("#calendar_inputField_"+vendorId).val();
                            tmpstoredata+=vendorId+':'+storeid+':'+datfield+'#';
                            tmphasStoredata= true;
                        }

                    }
                    window.storedata = tmpstoredata;
                    window.hasStoredata = tmphasStoredata;

                    let shippingAddress,
                        addressData,
                        loginFormSelector = 'form[data-role=email-with-possible-login]',
                        emailValidationResult = customer.isLoggedIn();

                    if (!quote.shippingMethod()) {
                        this.errorValidationMessage('Please specify a shipping method.');
                        return false;
                    }

                    if (!customer.isLoggedIn()) {
                        $(loginFormSelector).validation();
                        emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                    }

                    if (!emailValidationResult) {
                        $(loginFormSelector + ' input[name=username]').focus();
                    }

                    if (this.isFormInline) {
                        this.source.set('params.invalid', false);
                        this.source.trigger('shippingAddress.data.validate');

                        if (this.source.get('shippingAddress.custom_attributes')) {
                            this.source.trigger('shippingAddress.custom_attributes.data.validate');
                        };
                        if (this.source.get('params.invalid')
                            || !quote.shippingMethod().method_code
                            || !quote.shippingMethod().carrier_code
                            || !emailValidationResult
                        ) {
                            return false;
                        }

                        shippingAddress = quote.shippingAddress();
                        addressData = addressConverter.formAddressDataToQuoteAddress(
                            this.source.get('shippingAddress')
                        );

                        //Copy form data to quote shipping address object
                        for (let field in addressData) {
                            if (addressData.hasOwnProperty(field)
                                && shippingAddress.hasOwnProperty(field)
                                && typeof addressData[field] != 'function'
                            ) {
                                shippingAddress[field] = addressData[field];
                            }
                        }

                        if (customer.isLoggedIn()) {
                            shippingAddress.save_in_address_book = 1;
                        }
                        selectShippingAddress(shippingAddress);
                    }
                    return true;
                }
            });
        };
    }
);
