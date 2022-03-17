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
 * Class Validate
 * @package Ced\CsHyperlocal\Controller\Shiparea
 */
class Validate extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $requestHttp;

    /**
     * Validate constructor.
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Request\Http $requestHttp
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
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Request\Http $requestHttp,
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

        $this->csv = $csv;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->requestHttp = $requestHttp;
    }

    /**
     *  save ship area form data
     */
    public function execute()
    {

        $result = $this->resultJsonFactory->create();
        $files = $this->requestHttp->getFiles();
        $zipcode = $this->_importCsv($files['file']);
        if (count($zipcode) == 0)
        {
            return $result->setData(['error'=>true]);
        } else {
            return $result->setData(['error'=>false]);
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
                if ($row == 0 && count($data) > 1)
                {
                    break;
                }
                if ($row > 0) {
                    array_push($zipcodesarray, $data[0]);
                }
            }
        }
        return $zipcodesarray;
    }
}

