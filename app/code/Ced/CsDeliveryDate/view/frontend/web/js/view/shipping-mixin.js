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
 * @package   Ced_CsDeliveryDate
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'mage/url',
        'uiComponent',
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
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Ced_DeliveryDate/js/view/delivery-date-block'
    ],
    function (
        $,
        _,
        url,
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
        $t) {
        'use strict';
        var popUp = null;

        return function (Shipping) {
            return Shipping.extend({
                defaults: {
                    template: 'Ced_CsDeliveryDate/shipping'
                },

                initialize: function () {
                    var self = this;
                    this._super();

                    /*added*/
                    this.rates.subscribe(
                        function (grates) {
                            self.shippingRateGroups([]);
                            _.each(
                                grates, function (rate) {
                                    var carrierTitle = rate['carrier_title'];
                                    var carrier_code = rate['carrier_code'];
                                    if (self.shippingRateGroups.indexOf(carrierTitle) === -1 && carrier_code != 'vendor_rates') {
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

                shippingRateGroups: ko.observableArray([]),

                getFormattedPrice: function (price) {

                    if(window.checkoutConfig.shipping.csDDmoduleEnable){
                        $('.checkout-shipping-method').ready(function(){
                            setTimeout(function() {

                                $("#deliveryDate").css({'display':'none'});
                                // $("#deliveryDateConfiguration").html(' ');
                                $("#deliveryDate").html(' ');
                            }, 600);
                        });
                    }
                    return priceUtils.formatPrice(price, quote.getPriceFormat());
                },

                getRatesForGroup: function (shippingRateGroupTitle) {
                    return _.filter(
                        this.rates(), function (rate) {
                            return shippingRateGroupTitle === rate['carrier_title'];
                        }
                    );
                },

                selectVirtualMethod: function (shippingMethod) {
                    var inputid = shippingMethod.carrier_code+'_'+shippingMethod.method_code;
                    inputid = inputid.replace("~", "\\~");
                    
                    let arr, vendorId = 0;
                    arr = inputid.split('~');
                    if(arr.length > 1){
                        vendorId = arr[1];
                    }
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

                    var flagg = true;
                    var METHOD_SEPARATOR = ':';
                    var SEPARATOR = '~';
                    var rates = new Array();
                    var sortedrate = new Array();
                    $('.vendor-rates').each(
                        function (indx, elm) {

                            var flag = false;
                            $(elm).find('.radio').each(
                                function (i, inpt) {
                                    if (inpt.checked) {

                                        flag = true;
                                        /*Delivery date callender settings start*/

                                        var isCustomerLoggedIn = window.isCustomerLoggedIn;
                                        var cust_name = $(inpt).attr('id');
                                        var sortvendor = cust_name.split(SEPARATOR);
                                        var final_vendor_id = isNaN(parseInt(sortvendor[1])) ? 0 : parseInt(sortvendor[1]);
                                        cust_name = cust_name.replace('~', '_');
                                        var commentNoteId = '#comment_note_' + cust_name;
                                        var comment = '#comment_' + cust_name;
                                        var timestampId = '#timestamp_' + cust_name;
                                        var ddNoteId = '#ddNote_' + cust_name;

                                        /*Delivery date should not work with Storepickup Shipping*/
                                        if (shippingMethod.carrier_code != 'storepickupshipping') {
                                            if (final_vendor_id == 0) {
                                                var ddforguest = parseInt(window.checkoutConfig.shipping.delivery_date.ddforguest);
                                                var showDeliveryDate = false;
                                                if (ddforguest == 1) {
                                                    showDeliveryDate = true;
                                                } else {
                                                    /*return true if customer is loggedin otherwise return false*/
                                                    showDeliveryDate = !!(isCustomerLoggedIn);
                                                }
                                                /*if DD for guest is enable then show otherwise not*/
                                                if (showDeliveryDate) {
                                                    if (window.checkoutConfig.shipping.delivery_date) {

                                                        /*start: to disaply delivery date form of admin*/
                                                        document.getElementById("ddform_" + (inpt.value).replace('~', '_')).style.display = "block";
                                                        document.getElementById("ddform_" + (inpt.value).replace('~', '_')).classList.add('_required');


                                                       var timestampData = window.checkoutConfig.shipping.delivery_date.timestamp;
                                                        //var timestampData = "5";

                                                     // alert("hey");alert(timestampData);
                                                        var tData = '';
                                                        if (timestampData && timestampData.length > 0) {
                                                            for (var i in timestampData) {
                                                                tData += "<option value='" + timestampData[i] + "'>" + timestampData[i] + "</option>";
                                                            }
                                                            $(timestampId).html(tData);
                                                        } else {
                                                            var timestampVisibiltyId = '#visibleTimestamp_' + cust_name;
                                                            $(timestampVisibiltyId).html('');
                                                        }

                                                        var enablecommentonfrontend = window.checkoutConfig.shipping.delivery_date.enableComment;
                                                        if (enablecommentonfrontend != 1) {
                                                            $(comment).html('');
                                                        }

                                                        var cmtNote = "<p style='width:100%'>" + window.checkoutConfig.shipping.delivery_date.fieldNote + "</p>";
                                                        $(commentNoteId).html(cmtNote);

                                                        var ddNote = "<p style='width:100%;display: block'>" + window.checkoutConfig.shipping.delivery_date.remarks + "</p>";
                                                        $(ddNoteId).html(ddNote);


                                                        /*var weekday = window.checkoutConfig.shipping.delivery_date.weekDays;*/

                                                        var maxDate = parseInt(window.checkoutConfig.shipping.delivery_date.maxDate);
                                                        var sameDayDelivery = parseInt(window.checkoutConfig.shipping.delivery_date.sameDayDelivery);
                                                        var minDate = (sameDayDelivery == 1) ? 0 : 1;

                                                        var enabledDay = (!window.checkoutConfig.shipping.delivery_date.weekDays) ? [0, 1, 2, 3, 4, 5, 6] : window.checkoutConfig.shipping.delivery_date.weekDays;
                                                        maxDate = parseInt(Math.ceil(parseInt(maxDate)));


                                                        var tag = '.delivery_date_' + cust_name;
                                                        var delivery_date_ele = $(tag);

                                                        var options = {
                                                            minDate: minDate,
                                                            maxDate: maxDate,
                                                            showButtonPanel: true,
                                                            changeMonth: true,
                                                            changeYear: true,
                                                            beforeShowDay: function (date) {
                                                                var day = date.getDay();
                                                                if (date && enabledDay.indexOf(day) > -1) {
                                                                    return [true];
                                                                } else {
                                                                    return [false];
                                                                }
                                                            }
                                                        };

                                                        delivery_date_ele.datepicker(options);
                                                    }
                                                }
                                            }
                                            else if(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id]  != undefined) {

                                                /*if DD for gust is enable then show otherwise not*/
                                                var checkEnabled = ((typeof window.checkoutConfig.shipping.csdeliverydate[final_vendor_id]) !== 'undefined');
                                                var enableByVendor = (checkEnabled)?parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].enablesettings):false;
                                                /*if csdeliverydate data is set and enable by vendor then show otehr wise not*/
                                                if (checkEnabled && enableByVendor) {

                                                    var ddforguest = parseInt(window.checkoutConfig.shipping.delivery_date.ddforguest);
                                                    var ddforguest = parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].ddforguest);
                                                    var showDeliveryDate = false;
                                                    if (ddforguest == 1) {
                                                        showDeliveryDate = true;
                                                    } else {
                                                        /*return true if customer is loggedin otherwise return false*/
                                                        showDeliveryDate = !!(isCustomerLoggedIn);
                                                    }

                                                    if (showDeliveryDate) {
                                                        /*start: to disaply delivery date form of vendor's */
                                                        document.getElementById("ddform_" + (inpt.value).replace('~', '_')).style.display = "block";
                                                        document.getElementById("ddform_" + (inpt.value).replace('~', '_')).classList.add('_required');


                                                        var cmt = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].commentfieldnote;
                                                        $(commentNoteId).html(cmt);

                                                        var ddNote = "<p>" + window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].vddnoteforcalander + "</p>";
                                                        $(ddNoteId).html(ddNote);

                                                        var enablecommentonfrontend = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].enablecommentonfrontend;
                                                        if (enablecommentonfrontend != 1) {
                                                            $(comment).html('');
                                                        }
                                                        var timestampData = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].timestamp;

                                                        var tData = '';
                                                        if (timestampData && timestampData.length > 0) {
                                                            for (var i in timestampData) {
                                                                tData += "<option value='" + timestampData[i] + "'>" + timestampData[i] + "</option>";
                                                            }
                                                            $(timestampId).html(tData);
                                                        } else {
                                                            var timestampVisibiltyId = '#visibleTimestamp_' + cust_name;
                                                            $(timestampVisibiltyId).html('');
                                                        }

                                                        $(timestampId).html(tData);


                                                        var weekday = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].weekdays;

                                                        var arr = [];
                                                        if(typeof weekday != 'undefined') {
                                                            for (var i = 0; i < weekday.length; i++) {
                                                                var elem = parseInt(weekday[i]);
                                                                arr.push(elem);
                                                            }
                                                        }

                                                        var maxDate = parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].maxdays);

                                                        var sameDayDelivery = parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].sameDayDelivery);
                                                        var minDate = (sameDayDelivery == 1) ? 1 : 0;
                                                        /*var minDate = 1;*/

                                                        var alldaydelivery = parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].alldaydelivery);
                                                        if (alldaydelivery == 1) {
                                                            var enabledDay = [0, 1, 2, 3, 4, 5, 6];
                                                        } else {
                                                            var enabledDay = (!weekday) ? [0, 1, 2, 3, 4, 5, 6] : arr;
                                                        }


                                                        maxDate = parseInt(Math.ceil(parseInt(maxDate)));


                                                        var tag = '.delivery_date_' + cust_name;
                                                        var delivery_date_ele = $(tag);

                                                        var options = {
                                                            minDate: minDate,
                                                            maxDate: maxDate,
                                                            showButtonPanel: true,
                                                            changeMonth: true,
                                                            changeYear: true,
                                                            beforeShowDay: function (date) {
                                                                var day = date.getDay();
                                                                if (date && enabledDay.indexOf(day) > -1) {
                                                                    return [true];
                                                                } else {
                                                                    return [false];
                                                                }
                                                            }
                                                        };

                                                        delivery_date_ele.datepicker(options);

                                                    }
                                                }
                                            }
                                        }
                                        /*start: to disaply delivery date form */
                                        /*document.getElementById("ddform_"+(inpt.value).replace('~', '_')).style.display = "block";*/
                                        /*end: of disaply delivery date form */

                                        rates.push(inpt.value);
                                    }else{
                                        if ($(inpt).attr('id').replace('~', '_') != 'storepickupshipping_storepickupshipping') {
                                            if (document.getElementById("ddform_" + (inpt.value).replace('~', '_'))) {
                                                document.getElementById("ddform_" + (inpt.value).replace('~', '_')).style.display = "none";
                                            }
                                        }
                                    }
                                }
                            );
                            if (!flag) {
                                flagg = false;
                            }
                        }
                    );
                    if (flagg) {
                        for (var i = 0; i < rates.length; i++) {
                            var sortedValue = rates[i].split(SEPARATOR);
                            var pos = isNaN(parseInt(sortedValue[1])) ? 0 : parseInt(sortedValue[1]);
                            sortedrate[pos] = rates[i];
                        }
                        var rate = '';
                        for (var i = 0; i < sortedrate.length; i++) {
                            if (sortedrate[i] != undefined) {
                                if (rate) {
                                    rate = rate + METHOD_SEPARATOR + sortedrate[i];
                                } else {
                                    rate = sortedrate[i];
                                }
                            }
                        }
                        if (document.getElementById('s_method_vendor_rates_' + rate)) {
                            var event = new Event('click');
                            document.getElementById('s_method_vendor_rates_' + rate).dispatchEvent(event);
                            rate = rate.replace("~", "\\~");
                            $('#s_method_vendor_rates_'+rate).click();
                        }
                    }
                    return true;
                },

                validateShippingInformation: function () {
                    /*latest changes */
                    var flagg = true;
                    var rates = new Array();
                    $('.vendor-rates').each(
                        function(indx,elm){
                            var flag = false;
                            $(elm).find('.radio').each(
                                function(i,inpt){
                                    if(inpt.checked) {
                                        flag = true;
                                        rates.push(inpt.value);
                                    }
                                }
                            );
                            if(!flag) {
                                flagg = false;
                            }
                        }
                    );
                    if(!flagg) {
                        this.errorValidationMessage('Please select shipping method for each vendor.');
                        return false;
                    }
                    /*latest changes*/
                    if(quote.shippingMethod()){
                        var idCode = quote.shippingMethod().method_code;
                        var METHOD_SEPARATOR = ':';
                        var SEPARATOR = '~';
                        var methods = idCode.split(METHOD_SEPARATOR);
                        var deliveryDate = [];
                        var timestamp = [];
                        var comment = [];
                        var vendor = [];
                        if ((quote.shippingMethod().method_code).split('_')[0] != 'storepickupshipping') {

                            for (var i = 0; i < methods.length; i++) {
                                if (true) {
                                    var vendorId = (methods[i].split(SEPARATOR))[1];
                                    if (vendorId === 'undefined') {
                                        vendorId = '';
                                    }
                                    var dd = 'delivery_date_' + methods[i].replace('~', '_');
                                    var error = 'dderror_' + methods[i].replace('~', '_');
                                    var ts = 'timestamp_' + methods[i].replace('~', '_');
                                    var cmt = 'delivery_comment_' + methods[i].replace('~', '_');
                                    var cmtfield = 'delivery_' + methods[i].replace('~', '_');

                                    vendor[i] = vendorId;
                                    if(typeof(document.getElementsByName(dd)[0]) != 'undefined' && document.getElementsByName(dd)[0].value == ''){
                                        var checkEnabled = ((typeof window.checkoutConfig.shipping.csdeliverydate[vendorId]) !== 'undefined');
                                        var enableByVendor = (checkEnabled)?parseInt(window.checkoutConfig.shipping.csdeliverydate[vendorId].enablesettings):false;
                                        var enableForGuest = (enableByVendor) ? parseInt(window.checkoutConfig.shipping.csdeliverydate[vendorId].ddforguest) : false;
                                        var guestCheck =  (!enableForGuest)? !!(window.isCustomerLoggedIn): true;
                                        /*if csdeliverydate data is set and enable by vendor then show otehr wise not*/
                                        if (checkEnabled && enableByVendor && guestCheck) {
                                            document.getElementById(error).style.display = "block";
                                            return;
                                        }
                                    }else{
                                        document.getElementById(error).style.display = "none";
                                    }
                                    deliveryDate[i] = (document.getElementsByName(dd)[0] != undefined) ? document.getElementsByName(dd)[0].value : '';
                                    timestamp[i] = (document.getElementsByName(ts)[0] != undefined) ? document.getElementsByName(ts)[0].value : '';

                                    comment[i] = (document.getElementsByName(cmt)[0] != undefined ) ? document.getElementsByName(cmt)[0].value : '';
                                }
                            }
                        }
                        window.deliveryDate = deliveryDate;
                        window.timestamp = timestamp;
                        window.comment = comment;
                    }
                    var shippingAddress,
                        addressData,
                        loginFormSelector = 'form[data-role=email-with-possible-login]',
                        emailValidationResult = customer.isLoggedIn();

                    if (!quote.shippingMethod()) {
                        this.errorValidationMessage('Please specify a shipping method.');
                        return false;
                    }

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

                            if(!storeid){
                                this.errorValidationMessage('Please Select Stores.');
                                return false;
                            }

                            if(!datfield){
                                this.errorValidationMessage('Please Select Store Date.');
                                return false;
                            }

                        }

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
                        }

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
                        for (var field in addressData) {
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
