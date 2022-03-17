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

namespace Ced\CsSla\Helper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
/**
 * Class Data
 * @package Ced\CsSla\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * @var int
     */
    protected $_storeId = 0;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    protected static $_states;

    const STATE_CONFIRMED = "Confirmed";

    const STATE_CANCELLED = "Cancelled";

    const STATE_OPEN = null;
    const EMAIL_TEMPLATE = 'ced_csmarketplace/vsla/send_confirm_email_template';
    const EMAIL_CANCEL_TEMPLATE = 'ced_csmarketplace/vsla/send_cancel_email_template';
    const EMAIL_DISPATCH_TEMPLATE = 'ced_csmarketplace/vsla/send_disapatch_email_template';

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $state;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Translate\Inline\StateInterface $state
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $state,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    )
    {
        parent::__construct($context);

        $this->_scopeConfig = $context->getScopeConfig();
        $this->state = $state;
        $this->storeManager = $storeManager;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param $value
     * @return mixed
     */
    public function getStoreConfig($value)
    {
        $ConfigValue = $this->_scopeConfig->getValue($value, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $ConfigValue;
    }

    /**
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN => __('Pending'),
                self::STATE_CONFIRMED => __('Confirmed'),
                self::STATE_CANCELLED => __('Cancelled'),

            );
        }
        return self::$_states;
    }

    /**
     * @param $mail
     * @param $description
     * @param $Vname
     * @param $Vemail
     */
    public function sendTransactional($mail, $description, $Vname, $Vemail)
    {
        $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email');
        $senderName = "Admin";
        $storeId = $this->getStoreId();
        $this->state->suspend();
        try {
            $error = false;
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $Vsender = [
                'name' => $senderName,
                'email' => $senderEmail,
            ];
            
              /* email template */
                $dispatchTemplate = $this->scopeConfig->getValue(
                    self::EMAIL_DISPATCH_TEMPLATE,
                    ScopeInterface::SCOPE_STORE,
                    $storeId
                );

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($dispatchTemplate)// this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['answer' => $description, 'name' => $Vname])
                ->setFrom($Vsender)
                ->addTo($Vemail)
                ->getTransport();

            $transport->sendMessage();
            $this->state->resume();
            return;
        } catch (\Exception $e) {

            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
            );
            $this->_redirect('*/*/');
            return;
        }
    }

    /**
     * @param $mail
     * @param $description
     * @param $Vname
     * @param $Vemail
     */
    public function sendTransactionalforCancel($mail, $description, $Vname, $Vemail)
    {
        $senderEmail = $this->_scopeConfig->getValue('trans_email/ident_general/email');
        $senderName = "Admin";
        $storeId = $this->getStoreId();
        $this->state->create('\Magento\Framework\Translate\Inline\StateInterface')->suspend();
        try {
            $error = false;
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $Vsender = [
                'name' => $senderName,
                'email' => $senderEmail,
            ];

            
             /* email template */
            $cancelTemplate = $this->scopeConfig->getValue(
                self::EMAIL_CANCEL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($cancelTemplate)// this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['answer' => $description, 'name' => $Vname])
                ->setFrom($Vsender)
                ->addTo($Vemail)
                ->getTransport();

            $transport->sendMessage();
            $this->state->resume();
            return;
        } catch (\Exception $e) {

            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
            );
            $this->_redirect('*/*/');
            return;
        }
    }

    /**
     * @param $mail
     * @param $name
     * @param $description
     * @param $Vname
     * @param $Vemail
     */
    public function sendSlaEmail($mail, $name, $description, $Vname, $Vemail)
    {
        $senderEmail = $Vemail;
        $senderName = $Vname;
        $this->state->suspend();
        try {
            $error = false;
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $Vsender = [
                'name' => $senderName,
                'email' => $senderEmail,
            ];
            $storeId = $this->getStoreId();
            /* email template */
            $template = $this->scopeConfig->getValue(
                self::EMAIL_TEMPLATE,
                ScopeInterface::SCOPE_STORE,
                $storeId
            );
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($template)// this code we have mentioned in the email_templates.xml
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND, // this is using frontend area to get the template file
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['answer' => $description, 'name' => $name])
                ->setFrom($Vsender)
                ->addTo($mail)
                ->getTransport();

            $transport->sendMessage();
            $this->state->resume();
            return;
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.' . $e->getMessage())
            );
            $this->_redirect('*/*/');
            return;
        }
    }
     /*
     * get Current store id
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
 
    /*
     * get Current store Info
     */
    public function getStore()
    {
        return $this->storeManager->getStore();
    }
}
