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
namespace Ced\CsHyperlocal\Plugin\Adminhtml\Customer;

class Delete
{
    /**
     * Cart constructor.
     * @param \Ced\CsHyperlocal\Helper\Data $cshyperlocalHelper
     * @param array $data
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\Shiparea $shipareaModel,
        \Ced\CsMarketplace\Model\Vendor $vendorModel,
        array $data = []
    )
    {
        $this->shipareaModel = $shipareaModel;
        $this->vendorModel = $vendorModel;
    }

    /**
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $subject
     * @param \Closure $proceed
     * @param $customerId
     * @return mixed
     */
    public function aroundDeleteById(
        \Magento\Customer\Model\ResourceModel\CustomerRepository $subject,
        \Closure $proceed,
        $customerId
    ) {
        $vendor = $this->vendorModel->loadByCustomerId($customerId);
        if ($vendor)
        {
            $this->shipareaModel->getCollection()->addFieldToFilter('vendor_id',$vendor->getId())->walk('delete');
        }
        $result = $proceed($customerId);
        return $result;
    }
}