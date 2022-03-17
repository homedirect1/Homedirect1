<?php
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
 * @package     Ced_GoogleMap
 * @author 	    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\GoogleMap\Block\Customer\Address;


use Ced\GoogleMap\Helper\Data;
use Magento\Framework\Exception\LocalizedException;

class Register extends \Magento\Customer\Block\Form\Register
{

    public function getLocationHtml()
    {
        try {
            return $this->getLayout()
                ->createBlock('Magento\Customer\Block\Form\Register')
                ->setTemplate('Ced_GoogleMap::customer/address/location.phtml')
                ->toHtml();
        } catch (LocalizedException $e) {echo $e->getMessage();die(__FILE__);
        }

        return '';
    }

}
