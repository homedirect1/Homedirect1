require([
    'jquery'
], function (jQuery) {

    jQuery(document).ready(triggerPaymentStep);
    function triggerPaymentStep() {
        if (jQuery('#shipping-method-buttons-container > div > button').is(':visible')) { 
            jQuery("#shipping-method-buttons-container > div > button").trigger("click");
        } else {
            setTimeout(triggerPaymentStep, 50); //wait 50 ms, then try again
        }
    }
});

