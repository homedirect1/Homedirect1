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
              Component) {
        return Component.extend({

            initialize: function () {
                this._super();
            },
            enableModule: function () {
                var format = parseInt(window.checkoutConfig.shipping.delivery_date.deliverydate_config);
                if ((format == 1)) {
                    return true;
                } else {
                    return false;
                }
            },

            summaryDate: function () {
                    // fullScreenLoader.startLoader();
                var burl = window.checkoutConfig.shipping.baseurl;
                    jQuery.post(burl + 'deliverydate/index/index',
                        {}, function (data, status) {
                            // var obj = jQuery.parseJSON(data);
                            var obj = data;
                            if (parseInt(obj.enableModule) == 1) {
                                (obj.timestamp != null) ? jQuery("#tstmp").html('Timestamp'+ obj.timestamp) : jQuery("#tmstmp").html(' ');
                                (obj.dDate != null) ? jQuery("#ddate").html('Delivery Date'+ obj.dDate) : jQuery("#tabledd").html(' ');
                                (obj.deliveryComment != null) ? jQuery("#dmsg").html('Comment'+bj.deliveryComment) : jQuery("#showDmsg").html(' ');

                                // jQuery("#dmsg").html(obj.deliveryComment);
                                // jQuery("#tstmp").html(obj.timestamp);
                            }
                            return this;
                        });

                // fullScreenLoader.stopLoader();
            }

        });

    });
