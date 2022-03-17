define([
    'jquery',
    'underscore',
    'mage/template',
    'mage/smart-keyboard-handler',
    'mage/translate',
    'priceUtils',
    'mage/url'
], function ($, _, mageTemplate, keyboardHandler, $t, priceUtils,url) {
    'use strict';
    return function (widget) {
    $.widget('mage.SwatchRenderer',widget, {
        getProduct: function () {
            var products = this._CalcProducts();
            var customurl = url.build('csmultiseller/product/swatchConfigurableSeller');
            $.ajax({
                url: customurl,
                type: 'POST',
                data: {
                    swatchProductId: products
                },
                complete: function(response) {      
                    window.akanksha=       response;
                      $('#multiseller').html(response.responseText);
                },
                
            });
            return _.isArray(products) ? products[0] : null;
        }
    });
    return $.mage.SwatchRenderer;
}
});
