
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)A)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

var config = {
    config: {
        mixins: {
            // Send data when saving shipping information
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Ced_DeliveryDate/js/model/shipping-save-processor/payload-extender-mixin': false,
                'Ced_CsDeliveryDate/js/model/shipping-save-processor/payload-extender-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Ced_CsMultiShipping/js/view/shipping-mixin': false,
                'Ced_CsDeliveryDate/js/view/shipping-mixin': true
            }
        }
    }
};
