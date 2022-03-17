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

namespace Ced\CsHyperlocal\Observer\Adminhtml\Vendor;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;

/**
 * Class Delete
 * @package Ced\CsHyperlocal\Observer\Adminhtml\Vendor
 */
Class Delete implements ObserverInterface
{

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaModel;

    /**
     * Delete constructor.
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaModel
     * @param RequestInterface $request
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaModel,
        RequestInterface $request
    )
    {
        $this->shipareaModel = $shipareaModel;
        $this->request = $request;
    }

    /**
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorId = $this->request->getParam('vendor_id');
        $this->shipareaModel->create()->addFieldToFilter('vendor_id', $vendorId)->walk('delete');
        return $this;
    }
}

