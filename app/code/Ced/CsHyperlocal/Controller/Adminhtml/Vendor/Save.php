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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Vendor;

/**
 * Class Save
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Vendor
 */
class Save extends \Magento\Backend\App\Action
{
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
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory
     */
    protected $shipareaCollectionFactory;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaFactory;

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
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\App\Request\Http $requestHttp
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceData
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\Request\Http $requestHttp,
        \Ced\CsMarketplace\Helper\Data $marketplaceData,
        \Ced\CsHyperlocal\Model\ResourceModel\Shiparea\CollectionFactory $shipareaCollectionFactory,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
    )
    {
        $this->csv = $csv;
        $this->requestHttp = $requestHttp;
        $this->marketplaceData = $marketplaceData;
        $this->shipareaCollectionFactory = $shipareaCollectionFactory;
        $this->shipareaFactory = $shipareaFactory;
        $this->zipcodeFactory = $zipcodeFactory;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
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
            if (isset($postData['zipcode_type']) && $postData['zipcode_type'] == 'multiple' && $files['shiparea_form']['zipcode_import_csv']['name'] != '') {
                $zipcode = $this->_importCsv($files['shiparea_form']);
                if (count($zipcode) == 0) {
                    $this->messageManager->addErrorMessage(_('Invalid CSV file.'));
                    return $this->_redirect('*/vendor/shiparea');
                }
            }
        }


        $vendorIds = explode(',', $this->getRequest()->getPost('shiparea_form')['vendor_id']);
        if (count($vendorIds) > 0) {
            foreach ($vendorIds as $vendorId) {
                $shipareaCollection = $this->shipareaCollectionFactory->create()
                    ->addFieldToFilter('location', $postData['location'])
                    ->addFieldToFilter('vendor_id', $vendorId);
                $shipareaCollection->getSelect()->where("`is_origin_address` IS NULL OR `is_origin_address` = '0'");
                if ($shipareaCollection->count()) {
                    $alreadyExistVendorIds[] = $vendorId;
                } else {
                    $notexistVendorIds[] = $vendorId;
                    $locationid = $this->shipareaFactory->create()->saveData($postData, $vendorId, null, $postData['status']);
                    if (isset($postData['zipcode_type'])) {
                        if ($postData['zipcode_type'] == 'multiple') {
                            if (isset($files['shiparea_form']['zipcode_import_csv']['name']) && !empty($files['shiparea_form']['zipcode_import_csv']['name'])) {
                                $zipcode = $this->_importCsv($files['shiparea_form']);
                                if (count($zipcode) > 0) {
                                    foreach ($zipcode as $zip) {
                                        $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                                            ->addFieldToFilter('vendor_id', $vendorId)
                                            ->addFieldToFilter('zipcode', $zip);
                                        if ($isZipcodeexist->count() == 0) {
                                            $this->zipcodeFactory->create()->setLocationId($locationid)
                                                ->setVendorId($vendorId)
                                                ->setZipcode($zip)
                                                ->save();
                                        }
                                    }
                                }
                            }
                        } elseif ($postData['zipcode'] != '') {
                            $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                                ->addFieldToFilter('vendor_id', $vendorId)
                                ->addFieldToFilter('zipcode', $postData['zipcode']);
                            $this->zipcodeFactory->create()->setLocationId($locationid)
                                ->setVendorId($vendorId)
                                ->setZipcode($postData['zipcode'])
                                ->save();
                        }
                    }
                }
            }
        }

        if (isset($alreadyExistVendorIds) && count($alreadyExistVendorIds) > 0) {
            $this->_redirect('*/shiparea/index');
            $this->messageManager->addErrorMessage(__('A total of %1 vendor(s) location already exist.', count($alreadyExistVendorIds)));
        }
        if (isset($notexistVendorIds) && count($notexistVendorIds) > 0) {
            $this->_redirect('*/shiparea/index');
            $this->messageManager->addSuccessMessage(__('A total of %1 vendor(s) location saved sucessfully.', count($notexistVendorIds)));
        }
    }

    /**
     * @param $csvFile
     * @return array
     */
    protected
    function _importCsv($csvFile)
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

