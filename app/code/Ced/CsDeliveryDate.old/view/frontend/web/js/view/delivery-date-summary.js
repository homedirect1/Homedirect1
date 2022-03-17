/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/full-screen-loader'
    ],
    function (ko,
              Component,
              fullScreenLoader
    ) {  
        return Component.extend({
            /*
             * use to hide Ced_DeliveryDate fields from order summary field
             * */

            csdeliveryDateConfiguration:function(){
                // jQuery(".csDeliveryDateData").html('sadasdas');

                jQuery('.shipping-information-content').ready(function(){


                        // fullScreenLoader.startLoader();
                        var burl = window.checkoutConfig.shipping.baseurl;
                        jQuery.post(burl + 'csdeliverydate/index/index',
                            {}, function (obj) {

                                // var data = jQuery.parseJSON(obj);
                                var data = obj;
                                // var data = window.checkoutConfig.quoteItemData;
                                var dDate,dtstamp,cmnt,pname,html='',head='';
                                for(var i in data){
                                    pname = data[i]['name'];
                                    // alert(data[i]['cs_deliverydate']);
                                    if(data[i]['cs_deliverydate']) {
                                        head= '<br><b>Shipping Date and time :</b><br><hr>';
                                        dDate = new Date(data[i]['cs_deliverydate']);
                                        dDate = new Date(data[i]['cs_deliverydate']).toUTCString();
                                        dDate = dDate.split("18")[0];
                                        dtstamp = data[i]['cs_timestamp'];
                                        html += '<b>' + pname + '</b> - ' + dDate + ' , between-' + dtstamp + '<br>';
                                    }
                                }
                                html = head + html;

                                jQuery(".ship-to").append(html);
                            });

                        // fullScreenLoader.stopLoader();



                });

            }

        });

    });