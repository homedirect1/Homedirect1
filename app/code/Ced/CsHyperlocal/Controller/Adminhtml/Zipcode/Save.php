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
 * Class Save
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Zipcode
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\CsHyperlocal\Model\ZipcodeFactory
     */
    protected $_zipcodeModel;

    /**
     * @var \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory
     */
    protected $_zipcodeCollection;

    /**
     * @var \Ced\CsHyperlocal\Model\ShipareaFactory
     */
    protected $shipareaFactory;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
     * @param \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
        \Ced\CsHyperlocal\Model\ShipareaFactory $shipareaFactory
    )
    {
        $this->_zipcodeModel = $zipcode;
        $this->_zipcodeCollection = $zipcodeCollection;
        $this->shipareaFactory = $shipareaFactory;
        parent::__construct($context);
    }

    /**
     * @return mixed
     */
    public function execute()
    {
        $zipcode = $this->getRequest()->getPost('zipcode');
        $id = $this->getRequest()->getParam('id');
        $locationid = $this->getRequest()->getPost('location_id');
        $vendorIdByLocationId = $this->shipareaFactory->create()->getVendorIdByLocationId($locationid);
        if (!$id) {
            $isZipcodeexist = $this->_zipcodeCollection->create()->addFieldToFilter('location_id', $locationid)
                ->addFieldToFilter('zipcode', $zipcode);
            if ($isZipcodeexist->count() > 0) {
                $this->messageManager->addErrorMessage(_('Zipcode already exist.'));
                return $this->_redirect('*/shiparea/managezipcode', array('id' => $locationid));
            } else {
                $this->_zipcodeModel->create()->setVendorId($vendorIdByLocationId)
                    ->setLocationId($locationid)
                    ->setZipcode($zipcode)
                    ->save();
            }
        } else {
            $this->_zipcodeModel->create()->load($id)
                ->setZipcode($zipcode)
                ->save();
        }
        return $this->_redirect('*/shiparea/managezipcode', array('id' => $locationid));
        $this->messageManager->addSuccessMessage(_('You have successfully saved zipcode.'));
    }
}

