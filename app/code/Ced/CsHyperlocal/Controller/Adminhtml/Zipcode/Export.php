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

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Class Export
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Zipcode
 */
class Export extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaFactory;

    /**
     * Export constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
    )
    {
        parent::__construct($context);
        $this->_registry = $registry;
        $this->_fileFactory = $fileFactory;
        $this->shipareaFactory = $shipareaFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Exception
     */
    public function execute()
    {
        $vendorIdByLocationId = $this->shipareaFactory->create()->getVendorIdByLocationId($this->getRequest()->getParam('id'));
        $this->_registry->register('location_id',$this->getRequest()->getParam('id'));
        $this->_registry->register('vendor_id',$vendorIdByLocationId);


        $fileName = 'hyperlocal_zipcode.csv';
        $gridBlock = $this->_view->getLayout()->createBlock(
            'Ced\CsHyperlocal\Block\Shiparea\ExportGrid'
        );
        $content = $gridBlock->getCsvFile();
        return $this->_fileFactory->create($fileName, $content, DirectoryList::VAR_DIR);
    }
}
