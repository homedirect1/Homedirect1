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

namespace Ced\CsHyperlocal\Plugin\Vendor\Account;

use Ced\CsHyperlocal\Model\Shiparea;

/**
 * Class View
 * @package Magento\Catalog\Controller\Product
 */
class CreatePost
{
    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory
     */
    protected $vendorCollection;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaModel;

    /**
     * @var \Ced\CsHyperlocal\Helper\Data
     */
    protected $helperData;

    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * CreatePost constructor.
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shiparea
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     * @param \Ced\CsHyperlocal\Helper\Data $helperData
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Model\ResourceModel\Vendor\CollectionFactory $vendorCollection,
        \Magento\Framework\App\Request\Http $request,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shiparea,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory,
        \Ced\CsHyperlocal\Helper\Data $helperData,
        array $data = []
    )
    {
        $this->vendorCollection = $vendorCollection;
        $this->request = $request;
        $this->shipareaModel = $shiparea;
        $this->helperData = $helperData;
        $this->zipcodeFactory = $zipcodeFactory;
    }

    /**
     * @param \Magento\Catalog\Controller\Product\View $subject
     * @param \Closure $proceed
     * @return mixed
     */
    public function aroundExecute(\Ced\CsMarketplace\Controller\Account\CreatePost $subject, \Closure $proceed
    )
    {
        $value = $proceed();
        if ($this->helperData->isModuleEnabled()) {
            $vendorId = $this->vendorCollection->create()
                ->addFieldToFilter('email', $this->request->getPost('email'))
                ->getFirstItem()->getEntityId();
            $post = $this->request->getPost();

            /** save shiparea data */
            $locationId = $this->shipareaModel->create()
                ->saveData($this->request->getPost('vendor'), $vendorId, null, Shiparea::STATUS_ENABLED, Shiparea::ORIGIN_ADDRESS);

            /** save zipcode*/
            if (isset($post['vendor']['zipcode']) && $post['vendor']['zipcode'] != '') {

                $this->zipcodeFactory->create()->setLocationId($locationId)
                    ->setVendorId($vendorId)
                    ->setZipcode($post['vendor']['zipcode'])
                    ->save();
            }
        }
        return $value;
    }
}