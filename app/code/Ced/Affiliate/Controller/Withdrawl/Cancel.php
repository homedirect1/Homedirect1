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

namespace Ced\Affiliate\Controller\Withdrawl;

/**
 * Class Cancel
 * @package Ced\Affiliate\Controller\Withdrawl
 */
class Cancel extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_custmerSesion;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * Cancel constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_custmerSesion = $session;
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {

        if (!$this->_custmerSesion->isLoggedIn()) {
            $this->_redirect('affiliate/account/login');
            return;
        }

        $model = $this->affiliateWithdrawlFactory->create()->load($this->getRequest()->getParam('id'));
        $model->setStatus(\Ced\Affiliate\Model\AffiliateWithdrawl::CANCELLED);
        $model->save();
        $this->messageManager->addSuccessMessage(__('Withdrawal Request Has Been Cancelled Successfully'));
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('affiliate/withdrawl/index');
        return $resultRedirect;

    }
}
