/**
 * 
 */
define(
   [
       'jquery',
       'Magento_Checkout/js/view/summary/abstract-total'
   ],
   function ($,Component) {
       "use strict";
       return Component.extend({
           defaults: {
               template: 'Ced_Affiliate/checkout/summary/customdiscount'
           },
           isDisplayedCustomdiscount : function(){
               return discountdata.enable;
           },
           getCustomdiscount : function(){
        	   
        	   var price = -discountdata.discount;
               return this.getFormattedPrice(price);
               
           }
       });
   }
);