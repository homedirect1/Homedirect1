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


namespace Ced\GoogleMap\Plugin\Model;


use Magento\Framework\Registry;
use Magento\Quote\Api\Data\AddressInterface;

class ShippingMethodManagement
{
    const CHECKOUT_ADDRESS_FOR_RATES = 'checkout-address-for-rates';

    /**
     * Customer Address repository
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    protected $dataPersistor;

    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    ) {
        $this->addressRepository = $addressRepository;
        $this->dataPersistor = $dataPersistor;
    }

    public function aroundEstimateByAddressId(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        callable $proceed,
        $cartId,
        $addressId
    ) {
        $address = $this->addressRepository->getById($addressId);

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(__FILE__);
        $logger->info(__FUNCTION__);
        $logger->info($address->getCustomAttributes());
        $this->setAddressData($address->getCustomAttributes());
        return $proceed($cartId, $addressId);
    }

    public function aroundEstimateByAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        callable $proceed,
        $cartId,
        \Magento\Quote\Api\Data\EstimateAddressInterface $address
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(__FILE__);
        $logger->info(__FUNCTION__);
        $logger->info($address->getCustomAttributes());
        $this->setAddressData($address->getCustomAttributes());
        return $proceed($cartId, $address);
    }

    public function aroundEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        callable $proceed,
        $cartId,
        AddressInterface $address
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/shipping.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info(__FILE__);
        $logger->info(__FUNCTION__);
        $logger->info($address->getCustomAttributes());
        $this->setAddressData($address->getCustomAttributes());
        return $proceed($cartId, $address);
    }

    protected function setAddressData($data)
    {
        if ($this->dataPersistor->get(self::CHECKOUT_ADDRESS_FOR_RATES))
            $this->dataPersistor->clear(self::CHECKOUT_ADDRESS_FOR_RATES);
        $this->dataPersistor->set(self::CHECKOUT_ADDRESS_FOR_RATES, $data);
    }
}
