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

namespace Ced\CsHyperlocal\Model;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class VendorList
 * @package Ced\CsHyperlocal\Model
 */
class VendorList implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendor;

    /**
     * @var ResourceModel\Shiparea\CollectionFactory
     */
    protected $shiparea;

    /**
     * VendorList constructor.
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorModel
     * @param ResourceModel\Shiparea\CollectionFactory $shipareaModel
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VendorFactory $vendorModel,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaModel
    )
    {
        $this->vendor = $vendorModel;
        $this->shiparea = $shipareaModel;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $admin = false;
            $shipareaCollection = $this->shiparea->create();
            $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            $vendorIds = $shipareaCollection->getColumnValues('vendor_id');
            if (in_array(0, $vendorIds)) {
                $admin = true;
            }
            $vendorUniqueIds = array_unique($vendorIds);
            if (count($vendorUniqueIds) > 0) {
                foreach ($vendorUniqueIds as $vendorId) {
                    if ($vendorId != 0) {
                        $vendor = $this->vendor->create()->load($vendorId);
                        $this->options[] = ['value' => $vendor->getId(), 'label' => $vendor->getEmail()];
                    }
                }
            }

            if ($admin) {
                $this->options[] = ['value' => 0, 'label' => __('Admin')];
            }
        }
        return $this->options;
    }
}
