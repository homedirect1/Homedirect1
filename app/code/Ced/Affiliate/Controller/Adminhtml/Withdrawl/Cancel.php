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

namespace Ced\Affiliate\Controller\Adminhtml\Withdrawl;

use Magento\Backend\App\Action;

/**
 * Class Cancel
 * @package Ced\Affiliate\Controller\Adminhtml\Withdrawl
 */
class Cancel extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $_fileCsv;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * @var \Ced\Affiliate\Model\AmountSummaryFactory
     */
    protected $amountSummaryFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * Cancel constructor.
     * @param Action\Context $context
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     */
    public function __construct(
        Action\Context $context,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
    )
    {
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        $this->amountSummaryFactory = $amountSummaryFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return true;
    }


    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {

        $id = $this->getRequest()->getParam('id');
        $withdrawl = $this->affiliateWithdrawlFactory->create()->load($id);
        if (!$withdrawl->getIscredit()) {
            $amountSummary = $this->amountSummaryFactory->create()->load($withdrawl->getAffiliateId(), 'affiliate_id');
            $amountSummary->setRemainingAmount($amountSummary->getRemainingAmount() + $withdrawl->getRequestAmount());
            $amountSummary->save();
        } else {

            $amountSummary = $this->affiliateWalletFactory->create()->load($withdrawl->getCustomerId(), 'customer_id');
            $amountSummary->setRemainingAmount($amountSummary->getRemainingAmount() + $withdrawl->getRequestAmount());
            $amountSummary->save();
        }
        $withdrawl->setStatus(\Ced\Affiliate\Model\AffiliateWithdrawl::CANCELLED);
        $withdrawl->save();

        $this->messageManager->addSuccessMessage(__('Request Cancel Successfully'));
        $resultredirect = $this->resultRedirectFactory->create();
        $resultredirect->setPath('affiliate/withdrawl/index');
        return $resultredirect;
    }
}