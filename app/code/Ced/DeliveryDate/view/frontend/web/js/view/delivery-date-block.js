
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
        defaults: {
            template: 'Ced_DeliveryDate/delivery-date-block'
        },
        initialize: function () {
            this._super();
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

                var allWeekDay = window.checkoutConfig.shipping.delivery_date.fullhoursDelivery_weekdata;
                var weekday = window.checkoutConfig.shipping.delivery_date.weekDays;
                var allDayDelivery = window.checkoutConfig.shipping.delivery_date.allDayDelivery;
                var maxDate = parseInt(window.checkoutConfig.shipping.delivery_date.maxDate);
                var format = window.checkoutConfig.shipping.delivery_date.dateFormat;
                var sameDayDelivery = parseInt(window.checkoutConfig.shipping.delivery_date.sameDayDelivery);
                var hourMin = parseInt(window.checkoutConfig.shipping.delivery_date.sameDayDelivery_hourMin);
                var hourMax = parseInt(window.checkoutConfig.shipping.delivery_date.sameDayDelivery_hourMax);
                var enableTimeSelection = parseInt(window.checkoutConfig.shipping.delivery_date.enableTimeSelection);

                hourMax = hourMax - 1;

                var minDate = (sameDayDelivery == 1) ? 0 : 1;

                if (allDayDelivery == '1') {
                    var enabledDay = [0, 1, 2, 3, 4, 5, 6];
                } else {
                    var enabledDay = weekday;
                }

                ko.bindingHandlers.datetimepicker = {
                    init: function (element, valueAccessor, allBindingsAccessor) {
                        var $el = $(element);

                        /*if today's time is already over than for disabling current day*/
                        var currentHour = new Date().getHours();
                        /*minDate = (hourMax > currentHour) ? minDate : 1;*/

                        /*
                         prepare maxiumum delivery date visible on calendar
                         */

                        /*var currentDate = new Date();
                        maxDate = new Date(maxDate);
                        var diff = maxDate - currentDate;
                        currentDate = new Date(diff);
                        maxDate = Math.ceil(currentDate/(1000 * 60 * 60 * 24));
                        */
                        maxDate = Math.ceil(parseInt(maxDate));
                        maxDate = parseInt(maxDate);
                        /*preparing options for datepicker*/
                        var options = {
                            minDate: minDate,
                            maxDate: maxDate,
                            dateFormat: format,
                            beforeShowDay: function (date) {
                                var day = date.getDay();
                                if (date && enabledDay.indexOf(day) > -1) {
                                    return [true];
                                } else {
                                    return [false];
                                }
                            }
                        };

                        /* select DateTimePicker and DatePicker according to Admin  */
                        $el.datepicker(options);

                        /*var writable = valueAccessor();

                        if (!ko.isObservable(writable)) {
                            var propWriters = allBindingsAccessor()._ko_property_writers;
                            if (propWriters && propWriters.datetimepicker) {
                                writable = propWriters.datetimepicker;
                            } else {
                                return;
                            }
                        }
                        writable($(element).datetimepicker("getDate"));*/
                    },

                    /*update: function (element, valueAccessor) {
                        var widget = $(element).data("datetimepicker");
                        //when the view model is updated, update the widget
                        if (widget) {
                            var date = ko.utils.unwrapObservable(valueAccessor());
                            widget.date(date);
                        }
                    }*/

                };

                return this;
            }
            return false;
        }

    });
});
