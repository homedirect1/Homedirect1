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
    'mage/translate'
    ], function (
        $,
        mageTemplate,
        Component,
        validation,
        ko
    ) {
    'use strict';

     return Component.extend({
        allPriceList: ko.observableArray([]),
        rowCount: ko.observable(0),
        initialize: function () {
             $("body").on('click','.delete',function () {
                $(this).parent().parent().remove();
            });
            var self = this ;
            if (typeof window.termsData != "undefined" && window.termsData.length) {
                var count = 0;
                $.each(window.termsData, function (i,v) {
                    self.allPriceList.push(v);
                    if (parseInt(v.id) > count) {
                        count = v.id;
                    }
                });
                    this.rowCount(count);
            } else {
               self.allPriceList.push({
                   "id":0,
                   "title":"",
                   "term":"",
                   "price":"",
                   "repeat":"",
                   "time_span":"",
                   "payment_before_days":"",
                   "price_per_term":"",
                   "price_type":"0",
                   "no_of_terms":"",
                   "sort_order":""
                });
            }
            this._super();
        },
        inserRow: function () {
            var addRowNow;
            this.addRow = mageTemplate('#addfields-template');
            this.rowCount(parseInt(this.rowCount())+ 1);
            var rowId = this.rowCount();
                addRowNow = this.addRow({
                        data: {
                            "id": rowId,
                            "title":"",
                            "term":"",
                            "price":"",
                            "repeat":"",
                            "time_span":"",
                            "payment_before_days":"",
                            "price_per_term":"",
                            "price_type":"0",
                            "no_of_terms":"",
                            "sort_order":""
                        }
                    });
                $(addRowNow).appendTo($('#price_table'));
            },
        deleteRow : function (data, event) {
            console.log($(this).parent().parent());
        }.bind(this),
    })
    
});
