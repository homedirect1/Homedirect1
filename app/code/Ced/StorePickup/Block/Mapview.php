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
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Block;

/**
 * Class Mapview
 * @package Ced\StorePickup\Block
 */
class Mapview extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Ced\StorePickup\Model\StoreInfo
     */
    protected $_storesFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * Mapview constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Ced\StorePickup\Model\StoreInfo $storesFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Ced\StorePickup\Model\StoreInfo $storesFactory,
        array $data = []
    )
    {
        $this->_storesFactory = $storesFactory;
        $this->_urlInterface = $context->getUrlBuilder();
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getAllStores()
    {
        $collection = $this->_storesFactory->getCollection()
            ->addFieldToFilter('is_active', '1');
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
