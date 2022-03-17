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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Shiparea;

use Ced\CsHyperlocal\Model\Shiparea;

/**
 * Class Save
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Shiparea
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var Shiparea
     */
    protected  $_shipareaModel;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection
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
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceData;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $zipcodeCollectionFactory;

    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $zipcodeFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param Shiparea $shiparea
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection $shipareaCollection
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\App\Request\Http $requestHttp
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceData
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\CsHyperlocal\Model\Shiparea $shiparea,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\Collection $shipareaCollection,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\Request\Http $requestHttp,
        \Ced\CsMarketplace\Helper\Data $marketplaceData,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
    )
    {
        $this->_shipareaModel = $shiparea;
        $this->_shipareaCollection = $shipareaCollection;
        $this->csv = $csv;
        $this->requestHttp = $requestHttp;
        $this->marketplaceData = $marketplaceData;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
        $this->zipcodeFactory = $zipcodeFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $files = $this->requestHttp->getFiles();
        $postData = $this->getRequest()->getPost('shiparea_form');
        $filterType = $this->marketplaceData->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::FILTER_TYPE);

        if ($filterType == 'zipcode') {
            if (isset($postData['zipcode_type']) && $postData['zipcode_type'] == 'multiple' &&
                $files['shiparea_form']['zipcode_import_csv']['name'] != '') {
                $zipcode = $this->_importCsv($files['shiparea_form']);
                if (count($zipcode) == 0) {
                    $this->messageManager->addErrorMessage(_('Invalid CSV file.'));
                    return $this->_redirect('*/*/index');
                }
            }
        }

        $shipareaCollection = $this->_shipareaCollection->addFieldToFilter('location', $postData['location']);
        $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
        $shipareaCollection->addFieldToFilter('vendor_id', Shiparea::ADMIN_ID);
        $id = isset($postData['id']) ? $postData['id'] : '';
        $adminId = $postData['vendor_id'] != '' ? $postData['vendor_id'] : Shiparea::ADMIN_ID;
        if ($shipareaCollection->count() > 0 && !$id) {
            $this->_redirect('*/*/index');
            $this->messageManager->addErrorMessage(_('Delivery location already exists.'));
        } else {
            $locationid = $this->_shipareaModel->saveData($postData, $adminId, $id, $postData['status']);

            if (isset($postData['zipcode_type'])) {
                if ($postData['zipcode_type'] == 'multiple') {
                    if (isset($files['shiparea_form']['zipcode_import_csv']['name']) && !empty($files['shiparea_form']['zipcode_import_csv']['name'])) {
                        $zipcode = $this->_importCsv($files['shiparea_form']);
                        if (count($zipcode) > 0) {
                            foreach ($zipcode as $zip) {
                                $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                                    ->addFieldToFilter('vendor_id', $adminId)
                                    ->addFieldToFilter('zipcode', $zip);
                                if ($isZipcodeexist->count() == 0) {
                                    $this->zipcodeFactory->create()->setLocationId($locationid)
                                        ->setVendorId($adminId)
                                        ->setZipcode($zip)
                                        ->save();
                                }
                            }
                        }
                    }
                } elseif ($postData['zipcode'] != '') {
                    $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                        ->addFieldToFilter('vendor_id', $adminId)
                        ->addFieldToFilter('zipcode', $postData['zipcode']);
                    $this->zipcodeFactory->create()->setLocationId($locationid)
                        ->setVendorId($adminId)
                        ->setZipcode($postData['zipcode'])
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
        if (isset($csvFile['zipcode_import_csv']['tmp_name'])) {
            $csvData = $this->csv->getData($csvFile['zipcode_import_csv']['tmp_name']);
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

