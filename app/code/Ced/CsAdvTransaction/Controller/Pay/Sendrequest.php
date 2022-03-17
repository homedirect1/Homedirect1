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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Controller\Pay;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\UrlFactory;

/**
 * Class Sendrequest
 * @package Ced\CsAdvTransaction\Controller\Pay
 */
class Sendrequest extends \Ced\CsMarketplace\Controller\Vendor
{
    /**
     * @var
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $dateTime;

    /**
     * @var \Ced\CsAdvTransaction\Model\RequestFactory
     */
    protected $requestFactory;

    /**
     * Sendrequest constructor.
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Ced\CsAdvTransaction\Model\RequestFactory $requestFactory
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
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Ced\CsAdvTransaction\Model\RequestFactory $requestFactory,
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

        $this->dateTime = $dateTime;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $statsblock = $resultPage->getLayout()->getBlock('csadvtransaction_vendor_payments_sendrequest');
        $pendingAmount = $statsblock->getPendingPayAmount();
        $eligibleOrders = $statsblock->getVendorEligibleOrders()->getData();
        $date = $this->dateTime->gmtDate();
        $resultJson = $this->resultJsonFactory->create();
        try {
            foreach ($eligibleOrders as $eorders) {
                $ids[] = $eorders['order_id'];
            }
            $ids = implode(',', $ids);
            $vid = $this->getRequest()->getParam('vid');
            $Rmodel = $this->requestFactory->create()->getCollection()
                ->addFieldToFilter('vendor_id', $vid)->addFieldToFilter('status', \Ced\CsAdvTransaction\Model\Request::PENDING)
                ->getFirstItem();
            $loadId = 0;
            if (count($Rmodel->getData())) {
                $loadId = $Rmodel['id'];
            }

            if ($vid && $pendingAmount) {
                if (!$loadId) {
                    $Requestmodel = $this->requestFactory->create();
                    $Requestmodel->setVendorId($vid);
                    $Requestmodel->setAmount($pendingAmount);
                    $Requestmodel->setOrderIds($ids);
                    $Requestmodel->setStatus(\Ced\CsAdvTransaction\Model\Request::PENDING);
                    $Requestmodel->setCreatedAt($date);
                    $Requestmodel->save();
                } else {
                    $Requestmodel = $this->requestFactory->create()->load($loadId);
                    $Requestmodel->setAmount($pendingAmount);
                    $Requestmodel->setOrderIds($ids);
                    $Requestmodel->setStatus(\Ced\CsAdvTransaction\Model\Request::PENDING);
                    $Requestmodel->setCreatedAt($date);
                    $Requestmodel->save();
                }
            }

            return $resultJson->setData(1);

        } catch (\Exception $e) {

            return $resultJson->setData(0);

        }
    }
}
 
