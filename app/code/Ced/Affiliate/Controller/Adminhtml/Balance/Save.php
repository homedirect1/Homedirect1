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

namespace Ced\Affiliate\Controller\Adminhtml\Balance;

use Magento\Backend\App\Action;

/**
 * Class Save
 * @package Ced\Affiliate\Controller\Adminhtml\Balance
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Ced\Affiliate\Model\AffiliateWithdrawlFactory
     */
    protected $affiliateWithdrawlFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateWalletFactory
     */
    protected $affiliateWalletFactory;

    /**
     * @var \Ced\Affiliate\Model\AffiliateTransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Ced\Affiliate\Model\AmountSummaryFactory
     */
    protected $amountSummaryFactory;

    /**
     * Save constructor.
     * @param Action\Context $context
     * @param \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory
     * @param \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory
     * @param \Ced\Affiliate\Model\AffiliateTransactionFactory $transactionFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     */
    public function __construct(
        Action\Context $context,
        \Ced\Affiliate\Model\AffiliateWithdrawlFactory $affiliateWithdrawlFactory,
        \Ced\Affiliate\Model\AffiliateWalletFactory $affiliateWalletFactory,
        \Ced\Affiliate\Model\AffiliateTransactionFactory $transactionFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
    )
    {
        $this->affiliateWithdrawlFactory = $affiliateWithdrawlFactory;
        $this->affiliateWalletFactory = $affiliateWalletFactory;
        $this->transactionFactory = $transactionFactory;
        $this->amountSummaryFactory = $amountSummaryFactory;
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
        $data = $this->getRequest()->getPostValue();
        $withdrawl = $this->affiliateWithdrawlFactory->create();
        $withdrawl->addData($data);
        $withdrawl->setTransactionId($data['transaction_id']);

        $withdrawl->setStatus(1);
        $withdrawl->save();

        if ($data['payment_mode'] == 'storecredit') {
            $wallet = $this->affiliateWalletFactory->create();
            $walletdata = $wallet->load($withdrawl->getCustomerId(), 'customer_id');

            if ($walletdata->getData()) {
                $creditamount = $walletdata->getCreditAmount();
                $rem = $walletdata->getRemainingAmount();

                $wallet->setCreditAmount($data['amount_paid'] + $creditamount);
                $wallet->setRemainingAmount($rem + $data['amount_paid']);
                $wallet->setCustomerId($withdrawl->getCustomerId());
                $wallet->setCustomerEmail($withdrawl->getCustomerEmail());
                $wallet->setAffiliateId($withdrawl->getAffiliateId());
                $wallet->save();
            } else {
                $wallet = $this->affiliateWalletFactory->create();
                $wallet->setCreditAmount($data['amount_paid']);
                $wallet->setRemainingAmount($data['amount_paid']);
                $wallet->setCustomerId($withdrawl->getCustomerId());
                $wallet->setCustomerEmail($withdrawl->getCustomerEmail());
                $wallet->setAffiliateId($withdrawl->getAffiliateId());
                $wallet->save();
            }
            $transaction = $this->transactionFactory->create();
            $transaction->setWithdrawlRequestId($withdrawl->getId());
            $transaction->addData($data);
            $transaction->setAmountPaid($data['amount_paid']);
            $transaction->setCreatedAt(time());
            $transaction->setStatus('Paid');
            $transaction->setPaymentMode($data['payment_mode']);
            $transaction->setAffiliateId($withdrawl->getAffiliateId());
            $transaction->setCustomerId($withdrawl->getCustomerId());
            $transaction->save();


        } else {


            $transaction = $this->transactionFactory->create();
            $transaction->setWithdrawlRequestId($withdrawl->getId());
            $transaction->addData($data);
            $transaction->setAmountPaid($data['amount_paid']);
            $transaction->setCreatedAt(time());
            $transaction->setStatus('Paid');
            $transaction->setPaymentMode($data['payment_mode']);
            $transaction->setAffiliateId($withdrawl->getAffiliateId());
            $transaction->setCustomerId($withdrawl->getCustomerId());
            $transaction->save();

            $amountSummary = $this->amountSummaryFactory->create();
            $amountSummary->load($withdrawl->getAffiliateId(), 'affiliate_id');
            $earnedAmount = $amountSummary->getEarnedAmount();
            $remainingAmount = $amountSummary->getRemainingAmount();
            $amountSummary->setEarnedAmount((float)$earnedAmount + (float)$data['amount_paid']);
            $amountSummary->setRemainingAmount((float)$remainingAmount - (float)$data['amount_paid']);
            $amountSummary->save();

        }

        $this->messageManager->addSuccessMessage(__('Payment Done Successfully'));
        $resultredirect = $this->resultRedirectFactory->create();
        $resultredirect->setPath('affiliate/manage/edit', array('id' => $this->getRequest()->getParam('id')));
        return $resultredirect;


    }


}
