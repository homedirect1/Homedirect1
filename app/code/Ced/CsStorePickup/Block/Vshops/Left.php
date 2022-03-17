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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Block\Vshops;

/**
 * Class Left
 * @package Ced\CsStorePickup\Block\Vshops
 */
class Left extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * Left constructor.
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VendorFactory $vendorFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    )
    {
        $this->vendorFactory = $vendorFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\View\Element\Template
     */
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getshopurl()
    {
        $vendor_id = '';
        $shopurl = $this->getRequest()->getParam("shop_url");
        $vendor = $this->vendorFactory->create()->getCollection()->addAttributeToFilter('shop_url', $shopurl)->getData();
        foreach ($vendor as $key => $value) {
            $vendor_id = $value['entity_id'];
        }
        return $vendor_id;
    }
}