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

namespace Ced\CsHyperlocal\Ui\Component\Listing\Columns;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Vendorlist
 * @package Ced\CsHyperlocal\Ui\Component\Listing\Columns
 */
class Vendorlist implements OptionSourceInterface
{
    /**
     * @var \Ced\CsMarketplace\Model\VendorFactory
     */
    protected $vendor;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaCollectionFactory;

    /**
     * Vendorlist constructor.
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendorModel
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
     */
    public function __construct(
        \Ced\CsMarketplace\Model\VendorFactory $vendorModel,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
    )
    {
        $this->vendor = $vendorModel;
        $this->shipareaCollectionFactory = $shipareaCollectionFactory;
    }

    /**
     * @return array
     */
    public function getAllOptions()
    {
        $res = $this->getOptions();
        array_unshift($res, ['value' => '', 'label' => '']);
        return $res;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = [];
        $admin = false;
        $shipareaCollection = $this->shipareaCollectionFactory->create();
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
                    if ($vendor) {
                        $options[] = ['value' => $vendor->getId(), 'label' => $vendor->getEmail()];
                    }
                }
            }
        }
        if ($admin) {
            $options[] = ['value' => 0, 'label' => 'Admin'];
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }
}
