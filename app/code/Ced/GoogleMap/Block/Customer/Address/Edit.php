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

class Edit extends \Magento\Customer\Block\Address\Edit
{

    public function getLocationHtml()
    {
        try {
            return $this->getLayout()
                ->createBlock('Magento\Customer\Block\Address\Edit')
                ->setTemplate('Ced_GoogleMap::customer/address/location.phtml')
                ->setData('latitude', $this->getLocationLatitude())
                ->setData('longitude', $this->getLocationLongitude())
                ->toHtml();
        } catch (LocalizedException $e) {
        }

        return '';
    }

    public function getLocationLatitude()
    {
        $latitude = $this->getAddress()->getCustomAttribute(Data::LOCATION_LATITUDE_FIELD);
        return ($latitude) ? $latitude->getValue() : '';
    }

    public function getLocationLongitude()
    {
        $longitude = $this->getAddress()->getCustomAttribute(Data::LOCATION_LONGITUDE_FIELD);
        return ($longitude) ? $longitude->getValue() : '';
    }
}
