/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'uiComponent',
    ],
    function (ko,Component) {
        return Component.extend({
            summaryPoint1:function(){
                if(rewardsummary.summaryPoint){
                    return rewardsummary.summaryPoint+'Points';
                }else{
                    return false;
                }
            },
            customerLogin1: function(){
                if(rewardsummary.customerLogin){
                    return rewardsummary.customerLogin;
                }else{
                    return false;
                }
            },


        });

    });
