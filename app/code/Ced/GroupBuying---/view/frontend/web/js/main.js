
/**
 * Cedcoss
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category    Ced;
 * @package     Ced_GroupBuying
 * @author 		Cedcoss Magento Core Team <Cedcoss_MagentoCoreTeam@cedcoss.com>
 * @copyright   Copyright CEDCOSS Technologies (http://cedcoss.com/)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


function callFancyBox(id1) {
    var id = id1;
    require(['jquery', 'Magento_Ui/js/modal/modal'], function (jquery, modal) {
        jQuery('.price').attr('id', '-removed');
        jQuery('.regular-price').attr('id', '-removed');
        var overlay = jQuery('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
        overlay.appendTo(document.body);
        jQuery.ajax({
            type: 'POST',
            url: jQuery(id1).attr('value'),
        })
                .success(function (result) {
                    overlay.remove();
                    if (jQuery('#new').length) {
                        jQuery('#new').remove();
                    }
                    var newDivThingy = document.createElement("div");
                    newDivThingy.id = 'new';
                    document.getElementById('contentarea').appendChild(newDivThingy);

                    var options = {
                        type: 'popup',
                        responsive: true,
                        innerScroll: true,
                        title: 'Group Buying'
                    };

                    jQuery('#new').append(result);

                    var popup = modal(options, jQuery('#new'));

                    jQuery('#new').modal('openModal');

                });
    });
}

function customFormsubmit(id) {
    require(['jquery', 'Magento_Ui/js/modal/modal'], function (gGift, modal) {
        if (jQuery('#myform').validation('isValid') && jQuery('#myform').validation()) {
            var overlay = gGift('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
            overlay.appendTo(document.body);
            var formData = gGift('#product_addtocart_form').serializeArray();
            gGift.ajax({
                type: 'POST',
                url: gGift(id).attr('value'),
                data: formData,
            })
                    .success(function (result) {
                        overlay.remove();
                        if (jQuery('#new1').length) {
                            jQuery('#new1').remove();
                        }
                        var newDivThingy = document.createElement("div");
                        newDivThingy.id = 'new';
                        document.getElementById('contentarea').appendChild(newDivThingy);
                        var options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: true,
                            title: 'Group gift'
                        };

                        jQuery('#new').append(result);

                        var popup = modal(options, jQuery('#new'));

                        jQuery('#new').modal('openModal');
                    });
        } else {
            return false;
        }
    });

}

// for group gift custom form submit for grouped product

function customFormsubmit2(id) {
    require(['jquery', 'Magento_Ui/js/modal/modal'], function (gGift, modal) {
        //var myForm= new VarienForm('product_addtocart_form',true);
        if (jQuery('#myform1').validation('isValid') && jQuery('#myform1').validation()) {
            var overlay = gGift('<div id="overlay" style=" position: fixed; top: 0; left: 0; width: 100%; height: 100%;background-color: #000; filter:alpha(opacity=50); -moz-opacity:0.5;-khtml-opacity: 0.5;opacity: 0.5; z-index: 10000; display: block;"> </div>');
            overlay.appendTo(document.body);
            var formData = gGift('#myform1').serializeArray();
            var finalprice = 0;
            for (var i = 0; i < formData.length; i++) {
                if (formData[i].name.indexOf("super_group") >= 0 && formData[i].value > 0) {
                    var child_price = formData[i].name.split("super_group[");
                    var child_id = child_price[1].split("]");
                    var pro_id = child_id[0];
                    finalprice += formData[i].value;
                }
            }
            if (finalprice <= 0) {
                alert('Please specify the quantity of products to create group gift');
                return false;

            }
            alert(finalprice);
            gGift.ajax({
                type: 'POST',
                url: gGift(id).attr('value'),
                data: formData,
            })
                    .success(function (result) {
                        overlay.remove();
                        var newDivThingy = document.createElement("div");
                        newDivThingy.id = 'new1';
                        document.getElementById('contentarea').appendChild(newDivThingy);
                        var options = {
                            type: 'popup',
                            responsive: true,
                            innerScroll: true,
                            title: 'Group Buying'
                        };

                        jQuery('#new1').append(result);

                        var popup = modal(options, jQuery('#new1'));

                        jQuery('#new1').modal('openModal');
                    });
        } else {
            return false;
        }
    });
}

