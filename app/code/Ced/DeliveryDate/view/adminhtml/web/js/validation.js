require([
        'jquery',
        'mage/translate',
        'jquery/validate'],
    function($){
        $.validator.addMethod(
            'validate-and-show', function (v) {
                var elmnt = document.getElementById("deliverydate_delivery_maxDate");
                return ((parseInt(v) == v) ? elmnt.scrollIntoView(true) : elmnt.scrollIntoView(true));

            }, $.mage.__('Field must have length of 6'));
    }
);