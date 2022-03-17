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

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Data
 * @package Ced\Affiliate\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
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
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $pricingHelper;

    /**
     * @var \Ced\Affiliate\Model\Refersource
     */
    protected $refersource;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory
     */
    protected $comissionCollectionFactory;

    /**
     * @var \Ced\Affiliate\Model\AmountSummaryFactory
     */
    protected $amountSummaryFactory;

    /**
     * @var \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory
     */
    protected $withdrawlCollectionFactory;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricingHelper
     * @param \Ced\Affiliate\Model\Refersource $refersource
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory
     * @param \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory
     * @param \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $uploaderFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Ced\Affiliate\Model\Refersource $refersource,
        \Ced\Affiliate\Model\ResourceModel\AffiliateComission\CollectionFactory $comissionCollectionFactory,
        \Ced\Affiliate\Model\AmountSummaryFactory $amountSummaryFactory,
        \Ced\Affiliate\Model\ResourceModel\AffiliateWithdrawl\CollectionFactory $withdrawlCollectionFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->_storeManager = $storeManager;
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->filesystem = $filesystem;
        $this->uploaderFactory = $uploaderFactory;
        $this->scopeConfig = $context->getScopeConfig();
        $this->customerFactory = $customerFactory;
        $this->resourceConnection = $resourceConnection;
        $this->deploymentConfig = $deploymentConfig;
        $this->_urlBuilder = $context->getUrlBuilder();
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        $this->pricingHelper = $pricingHelper;
        $this->refersource = $refersource;
        $this->comissionCollectionFactory = $comissionCollectionFactory;
        $this->amountSummaryFactory = $amountSummaryFactory;
        $this->withdrawlCollectionFactory = $withdrawlCollectionFactory;
        parent::__construct($context);
    }


    /**
     * @return bool|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function uploadFile()
    {

        $mediaDirectory = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        $path = $mediaDirectory->getAbsolutePath('banner/files');
        $imagePath = false;
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'banner/files/';
        try {
            $uploader = $this->uploaderFactory->create(array('fileId' => "banner_data"));
            $uploader->setAllowedExtensions(array('png', 'jpg', 'jpeg', 'mp4', 'mkv', '3gp', 'flv'));
            $uploader->setAllowRenameFiles(false);
            $uploader->setFilesDispersion(false);
            $fileData = $uploader->validateFile();

            $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
            $fileName = $fileData['name'] . time() . '.' . $extension;
            $flag = $uploader->save($path, $fileName);
            $imagePath = true;
        } catch (\Exception $e) {
            return false;
        }
        return $fileName;

    }


    /**
     * @param $emails
     * @param $message
     * @param $subject
     * @param $referral_url
     * @return bool|int
     */
    public function sendInvitationEmail($emails, $message, $subject, $referral_url)
    {
        $support = $this->scopeConfig->getValue('referral/system/support_email');
        $customer = $this->_customerSession->getCustomer();
        $customer_Id = $customer->getId();
        $modeldata = $this->customerFactory->create()->load($customer_Id);
        $emailvariables['customername'] = $customer->getFirstname();
        $emailvariables['storename'] = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $emailvariables['subject'] = $subject;
        $emailvariables['message'] = $message;
        $emailvariables['referral_url'] = $referral_url;


        $this->_template = 'affiliate_invitation_referal_email';
        $this->_inlineTranslation->suspend();


        $adminname = $this->scopeConfig->getValue('general/store_information/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminname)
            $adminname = "Admin User";

        $adminemail = $this->scopeConfig->getValue('trans_email/ident_general/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if (!$adminemail)
            $adminemail = "owner@example.com";

        $sent = 0;
        try {
            foreach ($emails as $email) {
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
                        'email' => 'info@homedirect.in' /*$adminemail*/,
                    ])
                    ->addTo($email, 'Referral');

                $transport = $this->_transportBuilder->getTransport();
                $transport->sendMessage();
                $sent++;
            }
        } catch (\Exception $e) {

            return false;
        }
        $this->_inlineTranslation->resume();
        return $sent;
    }


    /**
     * @return mixed
     */
    public function isEnable()
    {
        $value = $this->scopeConfig->getValue('advanced/modules_disable_output/Ced_Affiliate');
        return $value;
    }

    /**
     * @param $key
     * @return string
     */
    public function getTableKey($key)
    {
        $tablePrefix = (string)$this->deploymentConfig
            ->get(\Magento\Framework\Config\ConfigOptionsListConstants::CONFIG_PATH_DB_PREFIX);
        $exists = $this->resourceConnection->getConnection('core_write')->showTableStatus($tablePrefix . 'permission_variable');
        if ($exists) {
            return $key;
        } else {
            return "{$key}";
        }
    }

    /**
     * @return string
     */
    public function checkAffiliate()
    {

        $model = $this->affiliateAccountFactory->create()
            ->load($this->_customerSession->getCustomer()->getId(), 'customer_id');
        $url = $this->_urlBuilder;
        if ($model->getData()) {
            $Uri = $url->getUrl('affiliate/account/index');
        } else {
            $Uri = $url->getUrl('affiliate/account/newAccount');
        }
        return $Uri;

    }

    /**
     * @param $document
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function uploadDocument($document)
    {

        $mediaDirectory = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        $customerId = $this->_customerSession->getCustomer()->getId();
        $path = $mediaDirectory->getAbsolutePath('affiliate/document/' . $customerId);
        $imagePath = false;
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'banner/files/';

        $filename = array();
        $fileUploaded = [];
        foreach ($document as $_document):
            if ($_document):
                try {

                    $uploader = $this->uploaderFactory->create(array('fileId' => $_document));

                    $uploader->setAllowedExtensions(array('png', 'jpg', 'jpeg', 'zip', 'txt', 'odt', 'zip', 'pdf', 'docx', 'xlsx', 'csv'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $fileData = $uploader->validateFile();

                    $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                    $fileName = $fileData['name'] . time() . '.' . $extension;
                    $fileName = str_replace(' ', '_', $fileName);

                    $filename[$_document] = $fileName;
                    $fileUploaded['document'] = $filename;
                    $flag = $uploader->save($path, $fileName);
                } catch (\Exception $e) {
                    continue;

                }
            endif;
        endforeach;
        return $fileUploaded;

    }


    /**
     * @param $document
     * @param $customer
     * @return array
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function uploadAdminDocument($document, $customer)
    {

        $mediaDirectory = $this->filesystem
            ->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);

        $customerId = $customer->getId();
        $path = $mediaDirectory->getAbsolutePath('affiliate/document/' . $customerId);
        $imagePath = false;
        $url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'banner/files/';

        $filename = array();
        $fileUploaded = [];
        foreach ($document as $_document):
            if ($_document):
                try {

                    /** @var \Magento\MediaStorage\Model\File\Uploader $uploader */
                    $uploader = $this->uploaderFactory->create(array('fileId' => $_document));

                    $uploader->setAllowedExtensions(array('png', 'jpg', 'jpeg', 'zip', 'txt', 'pdf', 'docx'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $fileData = $uploader->validateFile();

                    $extension = pathinfo($fileData['name'], PATHINFO_EXTENSION);
                    $fileName = $fileData['name'] . time() . '.' . $extension;

                    $fileName = str_replace(' ', '_', $fileName);
                    $filename[$_document] = $fileName;
                    $fileUploaded['document'] = $filename;
                    $flag = $uploader->save($path, $fileName);
                } catch (\Exception $e) {
                    throw new LocalizedException(__($e->getMessage()));
                }
            endif;
        endforeach;
        return $fileUploaded;

    }

    /**
     * @param $price
     * @return float|string
     */
    public function getFormattedPrice($price)
    {
        $priceHelper = $this->pricingHelper;
        $formattedPrice = $priceHelper->currency($price, true, false);
        return $formattedPrice;
    }

    /**
     * @return string
     */
    public function generatePromoCode()
    {
        $length = 6;
        $rndId = md5(uniqid(rand(), 1));
        $rndId = strip_tags(stripslashes($rndId));
        $rndId = str_replace(array(".", "$"), "", $rndId);
        $rndId = strrev(str_replace("/", "", $rndId));
        if (!is_null($rndId)) {
            return strtoupper(substr($rndId, 0, $length));
        }
        return strtoupper($rndId);
    }

    /**
     * @return bool
     */
    public function checkCustomer()
    {

        $Refersource = $this->refersource
            ->load($this->_customerSession->getCustomer()->getEmail(), 'referred_email');

        if ($Refersource->getData())
            return true;
        else
            return false;

    }

    /**
     * @return mixed
     */
    public function isAffiliateEnable()
    {

        $check = $this->scopeConfig->getValue('affiliate/general/activation',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $check;
    }

    /**
     * @return mixed
     */
    public function isEnableWthdrawlRequest()
    {

        $check = $this->scopeConfig->getValue('affiliate/withdrawl/withdrawl_request',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        return $check;
    }

    /**
     * @param $affiliateId
     * @return mixed
     */
    public function getAmount($affiliateId)
    {
        $orderStatus = $this->scopeConfig->getValue('affiliate/comission/add_comission_when',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $holdingDays = $this->scopeConfig->getValue('affiliate/comission/holding_time',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        $model = $this->comissionCollectionFactory->create()->addFieldToFilter('affiliate_id', $affiliateId);
        $model->addFieldToFilter('status', 'complete');

        if ($holdingDays && $holdingDays > 0) {
            $timeStamp = time();
            $toDate = date('Y-m-d H:i:s', $timeStamp);
            $fromDate = date('Y-m-d H:i:s', $timeStamp - 86400 * $holdingDays);
            $model->addFieldToFilter('create_at', array('lteq' => $fromDate));
        }

        $model->getSelect()->reset('columns')->columns(['total_amount' => 'SUM(comission)']);

        $amountSummary = $this->amountSummaryFactory->create()->load($affiliateId, 'affiliate_id');
        return $model->getData();

    }

    /**
     * @param $customerId
     * @return mixed
     */
    public function getAmountHistory($customerId)
    {

        $amountSummary = $this->withdrawlCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)->addFieldToFilter('status', '1');
        $amountSummary->getSelect()->reset('columns')->columns(['earned_amount' => 'SUM(request_amount)']);
        return $amountSummary->getData();
    }

    /**
     * @return bool|mixed
     */
    public function getTermsAndCondition()
    {

        $isEnabled = $this->scopeConfig->getValue('affiliate/account/terms_condition_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        if ($isEnabled) {
            return $this->scopeConfig->getValue('affiliate/account/terms_condition',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function IsFbEnabled()
    {
        return $this->scopeConfig->getValue('affiliate/referfriend/facebook_link',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function IsTwitterEnabled()
    {
        return $this->scopeConfig->getValue('affiliate/referfriend/twitter_link',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @return mixed
     */
    public function IsGoogleEnabled()
    {
        return $this->scopeConfig->getValue('affiliate/referfriend/google_link',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
