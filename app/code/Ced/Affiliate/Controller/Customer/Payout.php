<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Controller\Customer;

/**
 * Class Payout
 * @package Ced\Affiliate\Controller\Customer
 */
class Payout extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * Payout constructor.
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_getSession = $customerSession;
        $this->affiliateHelper = $affiliateHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {

        if (!$this->affiliateHelper->isAffiliateEnable()) {

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('customer/account/index');
            return $resultRedirect;
        }

        if (!$this->_getSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Please login first'));
            $this->_redirect('customer/account/login');
            return;
        }
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}