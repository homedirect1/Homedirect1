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

namespace Ced\Affiliate\Controller\Referral;

/**
 * Class SendInviteEmail
 * @package Ced\Affiliate\Controller\Referral
 */
class SendInviteEmail extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_getSession;

    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\AffiliateReferralFactory
     */
    protected $affiliateReferralFactory;

    /**
     * SendInviteEmail constructor.
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\AffiliateReferralFactory $affiliateReferralFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\AffiliateReferralFactory $affiliateReferralFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->_getSession = $customerSession;
        $this->affiliateHelper = $affiliateHelper;
        $this->affiliateReferralFactory = $affiliateReferralFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Zend_Validate_Exception
     */
    public function execute()
    {
        if ($this->affiliateHelper->isEnable() == "1") {
            $this->_redirect('*/*/index');
            return;
        }

        if (!$this->_getSession->isLoggedIn()) {
            $this->messageManager->addErrorMessage(__('Please login first'));
            $this->_redirect('affiliate/account/login');
            return;
        }
        $customer = $this->_getSession->getCustomer();
        $customer_Id = $customer->getId();
        $helper = $this->affiliateHelper;

        $emails = $this->getRequest()->getPost('emails');
        $emails = empty($emails) ? $emails : explode(',', $emails);

        $error = false;
        $subject = (string)$this->getRequest()->getPost('subject');
        $message = (string)$this->getRequest()->getPost('message');
        $referral_url = $this->getRequest()->getPost('referral_url');
        if ($message) {
            $message = nl2br(htmlspecialchars($message));
            if (empty($emails)) {
                $error = __('Please enter an email address.');
            } else {
                if ($emails) {
                    foreach ($emails as $index => $email) {
                        $email = trim($email);
                        if (!\Zend_Validate::is($email, 'EmailAddress')) {
                            $error = __('Please enter a valid email address.');
                            break;
                        }
                        $alreadyexist = $this->affiliateReferralFactory->create()
                            ->getCollection()
                            ->addFieldToFilter('referred_email', $email)
                            ->getData();
                        $referred_list = $this->affiliateReferralFactory->create();
                        $referred_list->setData('customer_id', $customer_Id);
                        $referred_list->setData('referred_email', $email);
                        $referred_list->setData('signup_status', 0);
                        $referred_list->save();
                        $emails[$index] = $email;
                    }
                }
            }
        }

        if ($error) {
            $this->messageManager->addErrorMessage($error);
            $this->_redirect('affiliate/referral/index');
            return;
        }
        $emails = array_unique($emails);

        $sendemail = $helper->sendInvitationEmail($emails, $message, $subject, $referral_url);

        if ($sendemail == false) {
            $this->messageManager->addErrorMessage(__('Unable To Send Email'));
        } else {
            $this->messageManager->addSuccessMessage(__('Your invitation is successfully sent to %1 recipients', $sendemail));
        }
        $this->_redirect('affiliate/referral/index');
        return;
    }
}