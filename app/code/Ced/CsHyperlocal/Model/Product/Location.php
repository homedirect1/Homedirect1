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

namespace Ced\CsHyperlocal\Model\Product;

class Location extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    public function __construct(
        \Ced\CsMarketplace\Model\Session $marketplaceSession,
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection $shipareaCollection,
        \Magento\Framework\App\RequestInterface $request,
        \Ced\CsMarketplace\Model\Vproducts $vproducts
    )
    {
        $this->marketplaceSession = $marketplaceSession;
        $this->marketplaceHelper = $marketplaceHelper;
        $this->shipareaCollection = $shipareaCollection;
        $this->request = $request;
        $this->vproducts = $vproducts;
    }

    /**
     * Get all options
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [];
        if ($this->marketplaceHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)) {

            /*For New Product Vendor Panel*/
            $vendorId = $this->marketplaceSession->getCustomerSession()->getVendorId();

            /*For Vendor product admin panel*/
            $proId = $this->request->getParam('id');
            $ifVendorProduct = '';
            if ($proId) {
                $ifVendorProduct = $this->vproducts->getVendorIdByProduct($proId);
            }
            if ($vendorId || $ifVendorProduct) {
                $vendorId = $vendorId ? $vendorId : $ifVendorProduct;
                $shipareaCollection = $this->shipareaCollection->addFieldToFilter('vendor_id', $vendorId);
                $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            } else {
                $shipareaCollection = $this->shipareaCollection->addFieldToFilter('vendor_id', \Ced\CsHyperlocal\Model\Shiparea::ADMIN_ID);
                $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
            }
            if ($shipareaCollection->count() != 0) {
                $this->_options[] = ['label' => __('Please select Location'), 'value' => ''];
                foreach ($shipareaCollection as $shiparea) {
                    $this->_options[] = ['label' => $shiparea->getLocation(), 'value' => $shiparea->getId()];
                }
            }
        }
        return $this->_options;
    }
}