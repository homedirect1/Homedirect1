define(['jquery'], function($) {
    'use strict';
  
    return function() {
        $.validator.addMethod(
            'validate-days',
            function(value, element) {
                return this.optional(element) || /^-?\d+$/.test(value);
            }, $.mage.__('Please enter a positive whole number in this field')
        );
    }
  });