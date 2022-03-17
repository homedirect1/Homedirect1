define([
    'jquery',
    'underscore',
    'mage/url'
], function ($, _,url) {
    'use strict';
    return function (widget) {

        $.widget('mage.configurable', widget, {
            _getSimpleProductId: function (element) {
                // TODO: Rewrite algorithm. It should return ID of
                //        simple product based on selected options.
               
                var allOptions = element.config.options,
                    value = element.value,
                    config;
    
                config = _.filter(allOptions, function (option) {
                    return option.id === value;
                });
                config = _.first(config);
    
                return _.isEmpty(config) ?
                    undefined :
                    _.first(config.allowedProducts);
    
            },
            
            _configureElement: function (element) {
               
                this.simpleProduct = this._getSimpleProductId(element);
            
                if (element.value) {
                    this.options.state[element.config.id] = element.value;

                    if (element.nextSetting) {
                        element.nextSetting.disabled = false;
                        this._fillSelect(element.nextSetting);
                        this._resetChildren(element.nextSetting);
                    } else {
                        if (!!document.documentMode) { //eslint-disable-line
                            this.inputSimpleProduct.val(element.options[element.selectedIndex].config.allowedProducts[0]);
                        } else {
                            this.inputSimpleProduct.val(element.selectedOptions[0].config.allowedProducts[0]);
                        }
                    }
                } else {
                    this._resetChildren(element);
                }

                this._reloadPrice();
                this._displayRegularPriceBlock(this.simpleProduct);
                this._displayTierPriceBlock(this.simpleProduct);
                this._displayNormalPriceLabel();
                this._changeProductImage();
                var customurl = url.build('csmultiseller/product/configurableSeller');
                $.ajax({
                    url: customurl,
                    type: 'POST',
                   
                    data: {
                        simpleProductId: this.simpleProduct
                    },
                    complete: function(response) {      
                        window.akanksha=response;
                        $('#multiseller').html(response.responseText); 
                    },
                    
                });
                }
            });

        return $.mage.configurable;
    }
});
