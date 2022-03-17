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
 * @package   Ced_CsMultiShipping
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
/*global define*/
define(
    [
        'jquery',
        "underscore",
        'mage/url',
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
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function(
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
        $t
    ) {
        'use strict';
        var popUp = null;
        return Component.extend(
            {
                defaults: {
                    template: 'Ced_CsDeliveryDate/shipping'
                },
                initialize: function () {
                    var self = this;
                    this._super();

                    this.rates.subscribe(
                        function (grates) {
                            self.shippingRateGroups([]);
                            _.each(
                                grates, function (rate) {
                                    var carrierTitle = rate['carrier_title'];
                                    var carrier_code = rate['carrier_code'];

                                    if (self.shippingRateGroups.indexOf(carrierTitle) === -1
                                        && carrier_code != 'vendor_rates') {
                                        self.shippingRateGroups.push(carrierTitle);
                                    }
                                }
                            );
                        }
                    );

                    return this;
                },


                getFormattedPrice: function (price) {

                    if(window.checkoutConfig.shipping.csDDmoduleEnable){
                        $('.checkout-shipping-method').ready(function(){
                            setTimeout(function() {

                                jQuery("#deliveryDate").css({'display':'none'});
                                // jQuery("#deliveryDateConfiguration").html(' ');
                                jQuery("#deliveryDate").html(' ');
                            }, 600);
                        });
                    }
                    return this._super();
                },

                selectVirtualMethod: function(shippingMethod) {

                    var inputid = shippingMethod.carrier_code+'_'+shippingMethod.method_code;
                    var flagg = true;
                    var METHOD_SEPARATOR = ':';
                    var SEPARATOR = '~';
                    var rates = new Array();
                    var sortedrate = new Array();
                    if (inputid.indexOf("storepickupshipping") >= 0){
                        var arr = inputid.split('~');

                        if(arr.length>1){
                            var vendorId = arr[1];
                        }
                        else{
                            var vendorId = 0;
                        }
                        var mapurl = url.build('csstorepickup/stores/getstores/vendor_id/'+vendorId);

                        $.ajax({
                            method: 'GET',
                            dataType: 'html',
                            url: mapurl,
                        }).success(function (result) {
                            $("#"+inputid).siblings('label').after(result);
                        });
                    }

                    jQuery('.vendor-rates').each(
                        function(indx,elm){
                            var flag = false;
                            jQuery(elm).find('.radio').each(
                                function(i,inpt){
                                    if(inpt.checked) {
                                        flag = true;

                                        var isCustomerLoggedIn = window.isCustomerLoggedIn;
                                        var cust_name = $(inpt).attr('id');
                                        var sortvendor = cust_name.split(SEPARATOR);
                                        var final_vendor_id = isNaN(parseInt(sortvendor[1])) ? 0 : parseInt(sortvendor[1]);
                                        cust_name = cust_name.replace('~', '_');
                                        var commentNoteId = '#comment_note_' + cust_name;
                                        var comment = '#comment_' + cust_name;
                                        var timestampId = '#timestamp_' + cust_name;
                                        var ddNoteId = '#ddNote_' + cust_name;

                                        rates.push(inpt.value);

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

                                                        var tData = '';
                                                        if (timestampData.length > 0) {
                                                            for (var i in timestampData) {
                                                                tData += "<option value='" + timestampData[i] + "'>" + timestampData[i] + "</option>";
                                                            }
                                                            jQuery(timestampId).html(tData);
                                                        } else {
                                                            var timestampVisibiltyId = '#visibleTimestamp_' + cust_name;
                                                            jQuery(timestampVisibiltyId).html('');
                                                        }

                                                        var enablecommentonfrontend = window.checkoutConfig.shipping.delivery_date.enableComment;
                                                        if (enablecommentonfrontend != 1) {
                                                            jQuery(comment).html('');
                                                        }

                                                        if(window.checkoutConfig.shipping.delivery_date && window.checkoutConfig.shipping.delivery_date.fieldNote){
                                                            var cmtNote = "<p style='width:100%'>" + window.checkoutConfig.shipping.delivery_date.fieldNote + "</p>";
                                                            jQuery(commentNoteId).html(cmtNote);
                                                        }

                                                        var ddNote = "<p style='width:100%;display: block'>" + window.checkoutConfig.shipping.delivery_date.remarks + "</p>";
                                                        jQuery(ddNoteId).html(ddNote);


                                                        //var weekday = window.checkoutConfig.shipping.delivery_date.weekDays;

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
                                                        jQuery(commentNoteId).html(cmt);

                                                        var ddNote = "<p>" + window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].vddnoteforcalander + "</p>";
                                                        jQuery(ddNoteId).html(ddNote);

                                                        var enablecommentonfrontend = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].enablecommentonfrontend;
                                                        if (enablecommentonfrontend != 1) {
                                                            jQuery(comment).html('');
                                                        }
                                                        var timestampData = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].timestamp;

                                                        var tData = '';
                                                        if (timestampData.length > 0) {
                                                            for (var i in timestampData) {
                                                                tData += "<option value='" + timestampData[i] + "'>" + timestampData[i] + "</option>";
                                                            }
                                                            jQuery(timestampId).html(tData);
                                                        } else {
                                                            var timestampVisibiltyId = '#visibleTimestamp_' + cust_name;
                                                            jQuery(timestampVisibiltyId).html('');
                                                        }

                                                        jQuery(timestampId).html(tData);


                                                        var weekday = window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].weekdays;

                                                        var arr = [];
                                                        for (var i = 0; i < weekday.length; i++) {
                                                            var elem = parseInt(weekday[i]);
                                                            arr.push(elem);
                                                        }

                                                        var maxDate = parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].maxdays);

                                                        var sameDayDelivery = parseInt(window.checkoutConfig.shipping.csdeliverydate[final_vendor_id].sameDayDelivery);
                                                        var minDate = (sameDayDelivery == 1) ? 1 : 0;
                                                        // var minDate = 1;

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
                                        // document.getElementById("ddform_"+(inpt.value).replace('~', '_')).style.display = "block";
                                        /*end: of disaply delivery date form */

                                    }else{
                                        if ($(inpt).attr('id').replace('~', '_') != 'storepickupshipping_storepickupshipping') {
                                            if (document.getElementById("ddform_" + (inpt.value).replace('~', '_'))) {
                                                document.getElementById("ddform_" + (inpt.value).replace('~', '_')).style.display = "none";
                                            }
                                        }
                                    }
                                }
                            );
                            if(!flag) {
                                flagg = false;
                            }
                        }
                    );


                    if(flagg) {
                        for(var i = 0; i < rates.length; i ++){
                            var sortedValue = rates[i].split(SEPARATOR);
                            var pos = isNaN(parseInt(sortedValue[1])) ? 0 : parseInt(sortedValue[1]);
                            sortedrate[pos] = rates[i];
                        }
                        var rate = '';
                        for(var i=0;i< sortedrate.length;i++){
                            if(sortedrate[i]!=undefined) {
                                if(rate) {
                                    rate = rate + METHOD_SEPARATOR + sortedrate[i];
                                }else{
                                    rate =  sortedrate[i];
                                }
                            }
                        }
                        if(document.getElementById('s_method_vendor_rates_'+rate)) {
                            var event = new Event('click');
                            document.getElementById('s_method_vendor_rates_'+rate).dispatchEvent(event);
                        }
                    }
                    return true;
                },



                validateShippingInformation: function () {
                    {
                        if (!quote.shippingMethod()
                            || !quote.shippingMethod().method_code
                            || !quote.shippingMethod().carrier_code
                        ) {
                            this.errorValidationMessage('Please refresh the page and select the shipping method again');
                            console.log('in first if to return false');
                            return false;
                        }
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
                                    if((document.getElementsByName(dd)[0].value == '')){
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
                    /*latest changes */
                    var flagg = true;
                    var rates = new Array();
                    jQuery('.vendor-rates').each(
                        function(indx,elm){
                            var flag = false;
                            jQuery(elm).find('.radio').each(
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
                    return this._super();
                }
            }
        );
    }
);
