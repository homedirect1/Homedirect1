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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Helper;

/**
 * Class Email
 * @package Ced\Affiliate\Helper
 */
class Email extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $_inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Email constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\Helper\Context $context
    )
    {
        $this->_storeManager = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }


    /**
     * @param $affiliateaccount
     * @param $isApprovalRequired
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendAccountCreationEmail($affiliateaccount, $isApprovalRequired)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();

        if ($isApprovalRequired)
            $emailvariables['msg'] = 'Thank You For Registering as Affiliate. Your Account is Admin Approval.On Approval You Will Get Confirmation Email';
        else
            $emailvariables['msg'] = 'Thank You For Registering as Affiliate.';
        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_account_creation_email';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($affiliateaccount->getCustomerEmail(), $affiliateaccount->getCustomerName());

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @param $status
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendConfirmationEmail($affiliateaccount, $status)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();

        if ($status == 1)
            $emailvariables['msg'] = 'Your Account Has Been Approved';
        else
            $emailvariables['msg'] = 'Your Account Has Been Disapproved';

        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)

            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_account_confirmation_email';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($affiliateaccount->getCustomerEmail(), $affiliateaccount->getCustomerName());

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendAccountDeleteEmail($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();


        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_account_delete_email';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($affiliateaccount->getCustomerEmail(), $affiliateaccount->getCustomerName());

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function notifyAdmin($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['email'] = $affiliateaccount->getCustomerEmail();


        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_accountcreate_notifyadmin_email';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($adminemail, $adminname);

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @param $status
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendAdminNotifyStatusMail($affiliateaccount, $status)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerEmail();

        if ($status == 1)
            $emailvariables['msg'] = 'You Recently Approved The Account';
        else
            $emailvariables['msg'] = 'You Recently Disapproved The Account';

        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_admin_notify_accountstatus';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($adminemail, $adminname);

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendWithdrawlRequestEmail($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerName();
        $emailvariables['request_amount'] = $affiliateaccount->getRequestName();
        $emailvariables['payment_mode'] = $affiliateaccount->getPaymentMode();


        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_withdrawl_request';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($affiliateaccount->getCustomerEmail(), $affiliateaccount->getCustomerName());

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendWithdrawlRequestNotifyAdminEmail($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerName();
        $emailvariables['request_amount'] = $affiliateaccount->getRequestName();
        $emailvariables['payment_mode'] = $affiliateaccount->getPaymentMode();


        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_withdrawl_request_adminnotify';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($adminemail, $adminname);

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }


    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendWithdrawlCancelRequest($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerName();
        $emailvariables['request_amount'] = $affiliateaccount->getRequestName();
        $emailvariables['payment_mode'] = $affiliateaccount->getPaymentMode();


        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_withdrawl_request_cancel';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($affiliateaccount->getCustomerEmail(), $affiliateaccount->getCustomerName());

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }
    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendWithdrawlCancelAdminNotify($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerName();
        $emailvariables['request_amount'] = $affiliateaccount->getRequestName();
        $emailvariables['payment_mode'] = $affiliateaccount->getPaymentMode();


        $storename = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_withdrawl_request_cancel_adminnotify';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($adminemail, $adminname);

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }

    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendPaymentEmail($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerName();
        $emailvariables['request_amount'] = $affiliateaccount->getRequestAmount();
        $emailvariables['amount_paid'] = $affiliateaccount->getAmountPaid();
        $emailvariables['payment_mode'] = $affiliateaccount->getPaymentMode();
        $emailvariables['transaction_id'] = $affiliateaccount->getTransactionId();

        $emailvariables['transaction'] = $affiliateaccount;

        $storename = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_payment_transaction';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($affiliateaccount->getCustomerEmail(), $affiliateaccount->getCustomerName());

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }
    }

    /**
     * @param $affiliateaccount
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function sendPaymentEmailAdminNotify($affiliateaccount)
    {

        $emailvariables['customername'] = $affiliateaccount->getCustomerName();
        $emailvariables['customeremail'] = $affiliateaccount->getCustomerName();
        $emailvariables['request_amount'] = $affiliateaccount->getRequestAmount();
        $emailvariables['amount_paid'] = $affiliateaccount->getAmountPaid();
        $emailvariables['payment_mode'] = $affiliateaccount->getPaymentMode();
        $emailvariables['transaction_id'] = $affiliateaccount->getTransactionId();


        $storename = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$storename)
            $storename = "Default Store";

        $emailvariables['storename'] = $storename;


        $adminname = $this->scopeConfig->getValue('general/store_information/name', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->_storeManager->getStore()->getId());
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";


        $this->_template = 'ced_affiliate_payment_transaction';
        $this->_inlineTranslation->suspend();
        $this->_transportBuilder->setTemplateIdentifier($this->_template)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->_storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($emailvariables)
            ->setFrom([
                'name' => $adminname,
                'email' => $adminemail,
            ])
            ->addTo($adminemail, $adminname);

        try {
            $transport = $this->_transportBuilder->getTransport();
            $transport->sendMessage();

            $this->_inlineTranslation->resume();
        } catch (\Exception $e) {

        }
    }

    /**
     * @param $template
     * @param $sender
     * @param array $templateParams
     * @param null $storeId
     * @return $this
     */
    protected function _sendEmailTemplateNew($template, $sender, $templateParams = array(), $storeId = null)
    {

        /*reference file vendor\magento\module-sales\Model\Order\Email\SenderBuilder.php */
        try {
            //$templateContainer=
            $vendor = $templateParams['vendor'];
            $transportBuilder = $this->_transportBuilder;
            $transportBuilder->addTo($vendor->getEmail(), $vendor->getName());
            $transportBuilder->setTemplateIdentifier($template);
            $transportBuilder->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId
                ]
            );

            $transportBuilder->setTemplateVars($templateParams);
            $transportBuilder->setFrom($this->scopeConfig->getValue($sender, $storeId));
            $transport = $transportBuilder->getTransport();

            $transport->sendMessage();

        } catch (\Exception $e) {
            echo $e->getMessage();
            die('====><><<>');
        }
        return $this;
    }

}
