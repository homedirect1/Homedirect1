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
namespace Ced\Affiliate\Block\Form;

use Magento\Customer\Model\AccountManagement;

/**
 * Customer register form block
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Register extends \Magento\Directory\Block\Data
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * Constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory
     * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
    	
        $this->_customerUrl = $customerUrl;
        $this->_moduleManager = $moduleManager;
        $this->_customerSession = $customerSession;
        parent::__construct(
            $context,
            $directoryHelper,
            $jsonEncoder,
            $configCacheType,
            $regionCollectionFactory,
            $countryCollectionFactory,
            $data
        );
        $this->_isScopePrivate = false;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Create New Affiliate Account'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->getUrl('affiliate/account/createpost');
    }
    public function isTermsAndConditionEnable(){
    	
    	return $this->_scopeConfig->getValue('affiliate/account/terms_condition_enable',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    public function getTermsAndCondition(){
    	 
    	return $this->_scopeConfig->getValue('affiliate/account/terms_condition',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
    	 return $this->getUrl('affiliate/account/login');
    }
    
    /**
     * Retrieve form data
     *
     * @return mixed
     */
    public function getFormData()
    {
    	$data = $this->getData('form_data');
    	if ($data === null) {
    		$formData = $this->_customerSession->getCustomerFormData(true);
    		$data = new \Magento\Framework\DataObject();
    		if ($formData) {
    			$data->addData($formData);
    			$data->setCustomerData(1);
    		}
    		if (isset($data['region_id'])) {
    			$data['region_id'] = (int)$data['region_id'];
    		}
    		$this->setData('form_data', $data);
    	}
    	return $data;
    }
    
    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
    	$countryId = $this->getFormData()->getCountryId();
    	if ($countryId) {
    		return $countryId;
    	}
    	return parent::getCountryId();
    }
    
    /**
     * Retrieve customer region identifier
     *
     * @return mixed
     */
    public function getRegion()
    {
    	if (null !== ($region = $this->getFormData()->getRegion())) {
    		return $region;
    	} elseif (null !== ($region = $this->getFormData()->getRegionId())) {
    		return $region;
    	}
    	return null;
    }
    
    /**
     * Newsletter module availability
     *
     * @return bool
     */
    public function isNewsletterEnabled()
    {
    	return $this->_moduleManager->isOutputEnabled('Magento_Newsletter');
    }
    
    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param \Magento\Customer\Model\Metadata\Form $form
     * @param string|null $scope
     * @return $this
     */
    public function restoreSessionData(\Magento\Customer\Model\Metadata\Form $form, $scope = null)
    {
    	if ($this->getFormData()->getCustomerData()) {
    		$request = $form->prepareRequest($this->getFormData()->getData());
    		$data = $form->extractData($request, $scope, false);
    		$form->restoreData($data);
    	}
    
    	return $this;
    }
    
    /**
     * Get minimum password length
     *
     * @return string
     */
    public function getMinimumPasswordLength()
    {
    	return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_MINIMUM_PASSWORD_LENGTH);
    }
    
    /**
     * Get number of password required character classes
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
    	return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }
    
}
