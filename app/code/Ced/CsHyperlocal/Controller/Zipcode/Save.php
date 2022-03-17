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

namespace Ced\CsHyperlocal\Controller\Zipcode;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Save
 * @package Ced\CsHyperlocal\Controller\Zipcode
 */
class Save extends \Ced\CsMarketplace\Controller\Vendor
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
     * Save constructor.
     * @param \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode
     * @param \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection
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
        \Ced\CsHyperlocal\Model\ZipcodeFactory $zipcode,
        \Ced\CsHyperlocal\Model\ResourceModel\Zipcode\CollectionFactory $zipcodeCollection,
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

        $this->_zipcodeModel = $zipcode;
        $this->_zipcodeCollection = $zipcodeCollection;
    }

    /**
     *  save ship area form data
     */
    public function execute()
    {
        $VendorId = $this->_getSession()->getVendorId();
        if (!$this->_getSession()->getVendorId()) {
            return;
        }
        $zipcode = $this->getRequest()->getPost('zipcode');
        $id = $this->getRequest()->getParam('id');
        $locationid = $this->getRequest()->getPost('location_id');

        if (!$id) {
            $isZipcodeexist = $this->_zipcodeCollection->create()->addFieldToFilter('location_id', $locationid)
                ->addFieldToFilter('zipcode', $zipcode);
            if ($isZipcodeexist->count() > 0) {
                $this->messageManager->addErrorMessage(_('Zipcode already exist.'));
                return $this->_redirect('*/shiparea/managezipcode', array('id'=>$locationid));
            } else {
                $this->_zipcodeModel->create()->setVendorId($VendorId)
                    ->setLocationId($locationid)
                    ->setZipcode($zipcode)
                    ->save();
            }
        } else {
            $this->_zipcodeModel->create()->load($id)
                ->setZipcode($zipcode)
                ->save();
        }
        return $this->_redirect('*/shiparea/managezipcode', array('id'=>$locationid));
        $this->messageManager->addSuccessMessage(_('You have successfully saved zipcode.'));
    }

}

