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

namespace Ced\CsStorePickup\Block;

use Ced\StorePickup\Model\StoreHourFactory;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Mapview
 * @package Ced\CsStorePickup\Block
 */
class Mapview extends Template
{
    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Ced\CsStorePickup\Model\StoreHourFactory
     */
    protected $_timeFactory;

    protected $_storesFactory;

    /**
     * Mapview constructor.
     * @param Context $context
     * @param UrlInterface $urlInterface
     * @param StoreHourFactory $storeHourFactory
     * @param StoreInfoFactory $storeInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlInterface,
        StoreHourFactory $storeHourFactory,
        StoreInfoFactory $storeInfoFactory,
        array $data = []
    )
    {
        $this->_request=$context->getRequest();
        $this->_timeFactory = $storeHourFactory;
        $this->_storesFactory = $storeInfoFactory;

        $this->_urlInterface = $urlInterface;
        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    public function getAllStores()
    {
        if ($this->getRequest()->getParam('vendor_id')) {
            $collection = $this->_storesFactory->create()->getCollection()
                ->addFieldToFilter('vendor_id', $this->getRequest()->getParam('vendor_id'))
                ->addFieldToFilter('is_active', '1');
        } else {
            $collection = $this->_storesFactory->create()->getCollection()
                ->addFieldToFilter('is_active', '1');
        }
        return $collection;
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlInterface->getBaseUrl();
    }

}