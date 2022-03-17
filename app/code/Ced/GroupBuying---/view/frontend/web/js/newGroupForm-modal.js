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
 * @category    Ced
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

/**
 * Jquery code to open modal
 */
define(
    [
        "jquery", "Magento_Ui/js/modal/modal"
    ],
    function ($) {
        let newGroupModal = {
            initModal: function (config, element) {
                let $target = $(config.target);
                $target.modal();
                let $element = $(element);
                $element.click(
                    function () {
                        $target.modal('openModal');
                    }
                );
            }
        };

        return {
            'newGroupForm-modal': newGroupModal.initModal
        };
    }
);
