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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Observer\Vendor;

use Magento\Framework\Event\ObserverInterface;

Class Registration implements ObserverInterface
{
    /**
     * @var
     */
    protected $csMarketplaceHelper;

    /**
     * Edit constructor.
     * @param \Ced\CsMarketplace\Helper\Data $csMarketplaceHelper
     */
    public function __construct(\Ced\CsMarketplace\Helper\Data $csMarketplaceHelper)
    {
        $this->_csMarketplaceHelper = $csMarketplaceHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_csMarketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)){
            $vAttributesCollection = $observer->getEvent()->getAttributes();
            $vAttributesCollection->getSelect()->where('vform.attribute_code != "location" AND vform.attribute_code != "latitude" AND vform.attribute_code != "longitude"');
            return $this;
        }
    }
}


