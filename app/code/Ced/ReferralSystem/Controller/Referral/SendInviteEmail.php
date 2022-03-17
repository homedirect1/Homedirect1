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
 * @package     Ced_ReferralSystem
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\ReferralSystem\Controller\Referral;

class SendInviteEmail extends \Magento\Framework\App\Action\Action {

    const REGISTERED = 1;
    const UNREGISTERED = 2;

    public function __construct(
        \Magento\Framework\App\Action\Context $context, 
        \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
        \Magento\Customer\Model\Session $customerSession,
        \Ced\ReferralSystem\Model\ResourceModel\ReferList\CollectionFactory $referListCollection,
        \Ced\ReferralSystem\Model\ReferListFactory $referList,
        \Ced\ReferralSystem\Helper\Data $helper,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\Escaper $_escaper,
        array $data = []
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->referListCollection = $referListCollection;
        $this->referList = $referList;
        $this->customerModel = $customerModel;
        $this->helper = $helper;
        $this->_escaper = $_escaper;
        parent::__construct ( $context, $data );
    }
    public function execute() {
        
        if ($this->helper->isEnable () == "1") {
            return $this->_redirect ( '*/*/index' );
        }
        
        if (! $this->customerSession->isLoggedIn ()) {
            $this->messageManager->addErrorMessage ( __ ( 'Please login first' ) );
            return $this->_redirect ( 'customer/account/login' );
        }

        $customer = $this->customerSession->getCustomer ();
        $customerId = $customer->getId ();

        $emails = $this->getRequest()->getPost('emails');
        $emails = empty($emails) ? $emails : explode(',', $emails);

        $error = false;
        $subject = (string)$this->getRequest()->getPost('subject');
        $message = (string)$this->getRequest()->getPost('message');

        $referral_url = $this->getRequest()->getPost('referral_url');

        if ($message) {
            $message = nl2br($this->_escaper->escapeHtml($message));

            if (empty($emails)) {
                $error = __('Please enter an email address.');
            } else {
                
                $invalidEmail = [];
                $already = [];
                $sending = [];
                
                foreach ($emails as $index => $email) {
                    $email = trim($email);
                    if (!\Zend_Validate::is($email, 'EmailAddress')) {
                        $invalidEmail[] = $email;
                    }else{
                        $alreadyRegistered = $this->customerModel->getCollection()
                            ->addFieldToFilter('email', $email)
                            ->getFirstItem();
    
                        if($alreadyRegistered && $alreadyRegistered->getId()){
                            $already[] = $email;
                        }else{
                            $alreadyexist = $this->referListCollection->create()
                                                ->addFieldToFilter('referred_email', $email)
                                                ->addFieldToFilter('customer_id', $customerId)
                                                ->getFirstItem();
                            if(!$alreadyexist->getId()){
                                $referlist = $this->referList->create();
                                $referlist->setData('customer_id', $customerId);
                                $referlist->setData('referred_email', $email);
                                $referlist->setData('signup_status', self::UNREGISTERED);
                                $referlist->save();
                            }else{
                                 $alreadyexist->setInviteDate(date('Y-m-d H:i:s'))->save();
                            }
                            $sending[] = $email;
                        }
                    }
                }
            }
        }
  
        if ($error) {
            $this->messageManager->addErrorMessage($error);
            return $this->_redirect('referralsystem/referral/index');
        }
        
        $sending = array_unique($sending);
        if(!empty($invalidEmail)){
            $this->messageManager->addErrorMessage(__('These email/s are invalid %1', implode(',', $invalidEmail)));
        }
        if(!empty($sending)){
            foreach ($sending as $key => $email) {
                $sendemail = $this->helper->sendInvitationEmail($email, $message, $subject, $referral_url);
                if($sendemail==false){
                    $this->messageManager->addErrorMessage(__('Unable To Send Email'));
                    return $this->_redirect('referralsystem/referral/index');
                }
            }
        }else{
            $sendemail = 0;
        }

        if(!empty($already)){
            if($sendemail==0){
                $this->messageManager->addErrorMessage(__("User(s) are  already registered %1", implode(',', $already)));
            }else{
                $this->messageManager->addSuccessMessage(
                    __('Your invitation is successfully sent to %1 recipients, and these are already registered %2',
                        $sendemail, implode(',', $already)));
            }
        }else{
            $this->messageManager->addSuccessMessage(__('Your invitation is successfully sent to %1 recipients', $sendemail));
        }
        
        return $this->_redirect('referralsystem/referral/index');
    }
}
