<?php

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
 * @package     Ced_Inventory
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsInventory\Block\Manage;

use Magento\Framework\View\Element\Template;

use Magento\Customer\Model\Session;

class Index extends \Magento\Framework\View\Element\Template
{
    private $_getSession;
    private $_objectManager;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        Session $customerSession,
        array $data = []
    )
    {
        $this->_getSession = $customerSession;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $data);
    }

    public function fieldData(){

        $vendorId = $this->_getSession->getData('vendor_id');
        $inventory = $this->_objectManager->create('\Ced\CsInventory\Model\Inventory');
        $data = $inventory->getCollection()->addFieldToFilter('vendor_id',$vendorId)->getData();
        return $data;
    }

}