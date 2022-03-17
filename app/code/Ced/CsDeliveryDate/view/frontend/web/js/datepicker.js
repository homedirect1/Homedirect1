
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
 * @category    Ced
 * @package     Ced_DeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

define([
    'jquery',
    'ko',
    'uiComponent'
], function ($, ko, Component) {
    'use strict';

    return Component.extend({

        initialize: function () {
            this._super();
            /*Delivery Date setting are enable by admin or not*/
            var deliveryDateConfig = parseInt(window.checkoutConfig.shipping.delivery_date.deliveryDateConfig);
            var isCustomerLoggedIn = window.isCustomerLoggedIn;
            var ddforguest = parseInt(window.checkoutConfig.shipping.delivery_date.ddforguest);

            var showDeliveryDate = false;
            if(deliveryDateConfig == 1 ){
                if(ddforguest == 1){
                    showDeliveryDate = true;
                }else{
                    /*return true if customer is loggedin otherwise return false*/
                    showDeliveryDate = !!(isCustomerLoggedIn);
                }

            }else{
                showDeliveryDate = false;
            }
            /*proceed further if all conditions are satisfied otherwise skip*/
            if (showDeliveryDate) {

                var calanderData = window.checkoutConfig.shipping.csdeliverydate;
                console.log(calanderData);
                var weekday = window.checkoutConfig.shipping.delivery_date.weekDays;
                var maxDate = parseInt(window.checkoutConfig.shipping.delivery_date.maxDate);
                var sameDayDelivery = parseInt(window.checkoutConfig.shipping.delivery_date.sameDayDelivery);

                var minDate = (sameDayDelivery == 1) ? 0 : 1;

                if (!weekday) {
                    var enabledDay = [0, 1, 2, 3, 4, 5, 6];
                } else {
                    var enabledDay = weekday;
                }


                ko.bindingHandlers.datetimepicker = {
                    init: function (element, valueAccessor, allBindingsAccessor) {
                        var $el = $(element);

                        maxDate = Math.ceil(parseInt(maxDate));
                        maxDate = parseInt(maxDate);
                        /*preparing options for datepicker*/
                        var options = {
                            minDate: minDate,
                            maxDate: maxDate,
                            beforeShowDay: function (date) {
                                var day = date.getDay();
                                if (date && enabledDay.indexOf(day) > -1) {
                                    return [true];
                                } else {
                                    return [false];
                                }
                            }
                        };

                        $el.datepicker(options);

                        var writable = valueAccessor();

                        if (!ko.isObservable(writable)) {
                            var propWriters = allBindingsAccessor()._ko_property_writers;
                            if (propWriters && propWriters.datetimepicker) {
                                writable = propWriters.datetimepicker;
                            } else {
                                return;
                            }
                        }
                        writable($(element).datetimepicker("getDate"));
                    },

                    update: function (element, valueAccessor) {
                        var widget = $(element).data("datetimepicker");
                        if (widget) {
                            var date = ko.utils.unwrapObservable(valueAccessor());
                            widget.date(date);
                        }
                    }

                };

                return this;
            }
        }

    });
});