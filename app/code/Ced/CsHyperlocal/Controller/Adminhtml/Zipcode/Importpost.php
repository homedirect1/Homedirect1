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

namespace Ced\CsHyperlocal\Controller\Adminhtml\Zipcode;

/**
 * Class Importpost
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Zipcode
 */
class Importpost extends \Magento\Backend\App\Action
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
     * Importpost constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\App\Request\Http $requestHttp
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\App\Request\Http $requestHttp,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcodeFactory,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollectionFactory
    )
    {
        $this->csv = $csv;
        $this->requestHttp = $requestHttp;
        $this->shipareaFactory = $shipareaFactory;
        $this->zipcodeFactory = $zipcodeFactory;
        $this->zipcodeCollectionFactory = $zipcodeCollectionFactory;
        parent::__construct($context);
    }


    /**
     *  import zipcode csv
     */
    public function execute()
    {
        $zipcode = '';
        $files = $this->requestHttp->getFiles();
        $locationid = $this->getRequest()->getParam('location_id');
        $zipcode = $this->_importCsv($files['import_csv']);
        if (count($zipcode) == 0) {
            $this->messageManager->addErrorMessage(_('Invalid CSV file.'));
            return $this->_redirect('*/shiparea/managezipcode', array('id' => $locationid));
        }
        $vendorIdByLocationId = $this->shipareaFactory->create()->getVendorIdByLocationId($locationid);

        try {
            if (isset($files['import_csv']['name'])) {
                $zipcode = $this->_importCsv($files['import_csv']);
                if (count($zipcode) > 0) {
                    foreach ($zipcode as $zip) {
                        $isZipcodeexist = $this->zipcodeCollectionFactory->create()
                            ->addFieldToFilter('location_id', $locationid)
                            ->addFieldToFilter('zipcode', $zip);
                        if ($isZipcodeexist->count() == 0) {
                            $this->zipcodeFactory->create()->setLocationId($locationid)
                                ->setVendorId($vendorIdByLocationId)
                                ->setZipcode($zip)
                                ->save();
                        }
                    }
                }
            }
            $this->messageManager->addSuccessMessage(_('Zipcode CSV Imported Successfully.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(_($e->getMessage()));
        }
        $this->_redirect('*/shiparea/managezipcode', array('id' => $locationid));

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

