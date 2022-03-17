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

namespace Ced\CsHyperlocal\Controller\Shiparea;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Save
 * @package Ced\CsHyperlocal\Controller\Shiparea
 */
class Save extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $_shipareaModel;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $_shipareaCollection;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $requestHttp;

    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollectionFactory;

    /**
     * Save constructor.
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shiparea
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollection
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\App\Request\Http $requestHttp
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Session $customerSession
     * @param UrlFactory $urlFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Ced\CsMarketplace\Model\VendorFactory $vendor
     */
    public function __construct(
        \Ced\CsHyperlocal\Model\ShipareaFactory $shiparea,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollection,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\Request\Http $requestHttp,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory,
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Session $customerSession,
        UrlFactory $urlFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Ced\CsMarketplace\Helper\Data $csmarketplaceHelper,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Ced\CsMarketplace\Model\VendorFactory $vendor
    )
    {
        parent::__construct(
            $context,
            $resultPageFactory,
            $customerSession,
            $urlFactory,
            $registry,
            $jsonFactory,
            $csmarketplaceHelper,
            $aclHelper,
            $vendor
        );

        $this->_shipareaModel = $shiparea;
        $this->_shipareaCollection = $shipareaCollection;
        $this->csv = $csv;
        $this->requestHttp = $requestHttp;
        $this->zipcodeFactory = $zipcodeFactory;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
    }

    /**
     *  save ship area form data
     */
    public function execute()
    {
        $zipcode = '';
        $VendorId = $this->_getSession()->getVendorId();
        if (!$this->_getSession()->getVendorId()) {
            return;
        }
        $files = $this->requestHttp->getFiles();
        $data = $this->getRequest()->getPost('cshyperlocal');
        $id = $this->getRequest()->getParam('id');
        $shipareaCollection = $this->_shipareaCollection->create()
            ->addFieldToFilter('location', $data['location'])
            ->addFieldToFilter('vendor_id', $VendorId);
        $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
        if ($shipareaCollection->count() > 0 && !$id) {
            $this->_redirect('*/*/index');
            $this->messageManager->addErrorMessage(_('Delivery location already exists.'));
        } else {
            $locationid = $this->_shipareaModel->create()->saveData($data, $VendorId, $id, $data['status']);
            if (isset($data['zipcode_type'])) {
                if ($data['zipcode_type'] == 'multiple') {
                    if (isset($files['zipcode_csv']['name']) && !empty($files['zipcode_csv']['name'])) {
                        $zipcode = $this->_importCsv($files['zipcode_csv']);
                        if (count($zipcode) > 0) {
                            foreach ($zipcode as $zip) {
                                $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                                    ->addFieldToFilter('vendor_id', $VendorId)
                                    ->addFieldToFilter('zipcode', $zip);
                                if ($isZipcodeexist->count() == 0) {
                                    $this->zipcodeFactory->create()->setLocationId($locationid)
                                        ->setVendorId($VendorId)
                                        ->setZipcode($zip)
                                        ->save();
                                }
                            }
                        }
                    }
                } elseif ($data['zipcode'] != '') {
                    $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                        ->addFieldToFilter('vendor_id', $VendorId)
                        ->addFieldToFilter('zipcode', $data['zipcode']);
                    $this->zipcodeFactory->create()->setLocationId($locationid)
                        ->setVendorId($VendorId)
                        ->setZipcode($data['zipcode'])
                        ->save();
                }
            }
            $this->_redirect('*/*/index');
            $this->messageManager->addSuccessMessage(_('You have successfully saved ship area.'));
        }
    }

    /**
     * @param $csvFile
     * @return array
     */
    protected function _importCsv($csvFile)
    {
        $zipcodesarray = [];
        if (isset($csvFile['tmp_name'])) {
            $csvData = $this->csv->getData($csvFile['tmp_name']);
            $zipcodesarray = [];
            foreach ($csvData as $row => $data) {
                if ($row > 0) {
                    array_push($zipcodesarray, $data[0]);
                }
            }
        }
        return $zipcodesarray;
    }
}

