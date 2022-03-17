/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
const config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/shipping': {
                'Ced_StorePickup/js/shipping-mixin': true
            },
            // Send data when saving shipping information
            'Magento_Checkout/js/model/shipping-save-processor/payload-extender': {
                'Ced_StorePickup/js/shipping-save-processor/payload-extender-mixin': true
            },
        }
    }
};
