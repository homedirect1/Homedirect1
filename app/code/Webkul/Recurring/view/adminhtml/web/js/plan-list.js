/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
define([
    'jquery',
    'mage/template',
    'uiComponent',
    'mage/validation',
    'ko',
    'Magento_Ui/js/modal/modal',
    'mage/translate'
    ], function (
        $,
        mageTemplate,
        Component,
        validation,
        ko,
        modal,
        $t
    ) {
    'use strict';

     return Component.extend({
        allPlanList: ko.observableArray([]),
        initialFee : ko.observableArray([]),
        storeId    : ko.observable('0'),
        initialize: function () {
            var self = this ;
            if (parseInt(window.storeId) == 0) {
                self.storeId(true);
            } else {
                self.storeId(false);
            }
            var buttonId = '#planButton';
            var checkBox = '.wkcheckbox';
            var options = {
                type: 'slide',
                responsive: true,
                innerScroll: true,
                title: $t("Subscriptions Types"),
                buttons: [{
                    text: $t("Continue"),
                    class: '',
                    click: function () {
                        var modal = this;
                        var planInfo    = $('#planid');
                        var feeArray    = $('.plan_initial_fee');
                        var bodyArray   = $('.wk-pricebody');
                        var dataString  = '';
                        
                        bodyArray.each(function (key, value) {
                            var isChecked = false;
                            var eleCheckBox = $(value).find(checkBox);
                            if (eleCheckBox.is(':checked')) {
                                self.checkValue(value, "plan_name");
                                self.checkValue(value, "plan_initial_description");
                                self.checkValue(value, "plan_initial_fee");
                                self.checkValue(value, "plan_subscription_charge");
                            }
                        });
                          if ($('.shouldFill').length == 0) {
                            modal.closeModal();
                          }
                    }
                }]
            };


            /**
             *  this block will select and deselect all rows.
             */
            $('body').on('click', '.wkmasscheck', function () {
                if ($(this).is(':checked')) {
                    $.each($(checkBox), function () {
                        if (!$(this).is(':checked')) {
                            $(this).click();
                        }
                    });
                } else {
                    $.each($(checkBox), function () {
                        $(this).click();
                    });
                }
            });
            /**
             *  this block validates configuration values.
             */
            $('body').on('click', checkBox, function () {
                var parentEle = $(this).parent().parent().parent();
                if ($(this).is(':checked')) {
                    if (parentEle.find('.plan_initial_fee').length) {
                        parentEle.find('.plan_initial_fee').removeAttr('disabled');
                    }
                    parentEle.find('.plan_initial_description').removeAttr('disabled');
                    parentEle.find('.plan_name').removeAttr('disabled');
                    parentEle.find('.plan_engine').removeAttr('disabled');
                    parentEle.find('.plan_subscription_charge').removeAttr('disabled');
                } else {
                    parentEle.find('.wkrmcolor').css('border','grey 1px solid');
                    parentEle.find('.wkrmcolor').removeClass("shouldFill");
                    if (parentEle.find('.plan_initial_fee').length) {
                        parentEle.find('.plan_initial_fee').attr('disabled','disabled');
                    }
                    parentEle.find('.plan_initial_description').attr('disabled','disabled');
                    parentEle.find('.plan_name').attr('disabled','disabled');
                    parentEle.find('.plan_engine').attr('disabled','disabled');
                    parentEle.find('.plan_subscription_charge').attr('disabled','disabled');
                }
            });

            /**
             *  this function validates integer values
             */
            $('body').on('keyup', '.plan_initial_fee' ,function () {
                self.validateValues($(this));
            });

            /**
             *  this function validates integer values
             */
            $('body').on('keyup', '.plan_subscription_charge' ,function () {
                self.validateValues($(this));
            });

            $('body').on('click', '[data-index=subscription-configuration]' ,function () {
                if ($(this).find('select').val() == 1) {
                    $(buttonId).show();
                }
                if (!$(this).find(buttonId).length) {
                    $(this).find('select').parent().after($(buttonId));
                }
            });

            /**
             *  this block fill in the subscripiton grid values.
             */
            if (typeof window.plansData != "undefined" && window.plansData.length) {
                $.each(window.plansData, function (i,v) {
                    self.allPlanList.push(v);
                });
            }
            this._super();
            
            /**
             *  this function is responsible for toogling the subscriptions configurations options
             */
            $('body').on('change', 'select[name="product[subscription]"]' ,function () {
                if ($(this).val() == 1) {
                    $(buttonId).show();
                } else {
                    $(buttonId).hide();
                }
            });

            /**
             *  this function is responsible for opening the subscription modal
             */
            $('body').on('click', buttonId ,function () {
                    var popup = modal(options, $('#wkplanlist'));
                    $('#wkplanlist').modal('openModal');
            });
        },
        checkValue : function (element,wkclass) {
            if ($(element).find('.'+ wkclass).val()) {
                $(element).find('.'+ wkclass).removeClass("shouldFill");
                $(element).find('.'+ wkclass).css('border','grey 1px solid');
                return $(element).find('.'+ wkclass).val();
            } else {
                $(element).find('.'+ wkclass).css('border','1px solid #FF0B17');
                $(element).find('.'+ wkclass).addClass("shouldFill");
                return '0';
            }
        },
        validateValues : function (element) {
            if (element.val()) {
               if (!$.isNumeric(element.val())) {
                    element.val("");
               }
            }
        },
        canVisibleth : function (element) {
            var storeId = self.storeId();
            if (parseInt(storeId) == 0) {
                return true;
            }
            return false;
        }
    })
});
