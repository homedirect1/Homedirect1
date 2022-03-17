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

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\AccountManagement;
use Magento\Customer\Api\AccountManagementInterface;
/**
 * Customer edit form block
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Edit extends \Magento\Customer\Block\Account\Dashboard
{
	protected $_coreRegistry;
	protected $_identityType;
	
	public function __construct(
			\Magento\Framework\View\Element\Template\Context $context,
			\Magento\Customer\Model\Session $customerSession,
			\Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
			CustomerRepositoryInterface $customerRepository,
			\Magento\Framework\Registry $registry,
			AccountManagementInterface $customerAccountManagement,
			\Ced\Affiliate\Model\IdentityType $identityType,
			array $data = []
	) {
		$this->_identityType = $identityType;
		$this->_coreRegistry = $registry;
		$this->customerSession = $customerSession;
		$this->subscriberFactory = $subscriberFactory;
		$this->customerRepository = $customerRepository;
		$this->customerAccountManagement = $customerAccountManagement;
		parent::__construct($context, $customerSession, $subscriberFactory, $customerRepository, $customerAccountManagement);
	}
	
    /**
     * Retrieve form data
     *
     * @return array
     */
    protected function getFormData()
    {
        $data = $this->getData('form_data');
        if ($data === null) {
            $formData = $this->customerSession->getCustomerFormData(true);
            $data = [];
            if ($formData) {
                $data['data'] = $formData;
                $data['customer_data'] = 1;
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Restore entity data from session. Entity and form code must be defined for the form.
     *
     * @param \Magento\Customer\Model\Metadata\Form $form
     * @param null $scope
     * @return \Magento\Customer\Block\Form\Register
     */
    public function restoreSessionData(\Magento\Customer\Model\Metadata\Form $form, $scope = null)
    {
        $formData = $this->getFormData();
        if (isset($formData['customer_data']) && $formData['customer_data']) {
            $request = $form->prepareRequest($formData['data']);
            $data = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }

    /**
     * Return whether the form should be opened in an expanded mode showing the change password fields
     *
     * @return bool
     *
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getChangePassword()
    {
        return $this->customerSession->getChangePassword();
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
     * Get minimum password length
     *
     * @return string
     */
    public function getRequiredCharacterClassesNumber()
    {
        return $this->_scopeConfig->getValue(AccountManagement::XML_PATH_REQUIRED_CHARACTER_CLASSES_NUMBER);
    }

    public function getIdentityFileHref(){
    	return $this->getFileSrc().$this->_coreRegistry->registry('current_account')->getIdentityfile();
    }
    public function getAddressFileHref(){
    	return $this->getFileSrc().$this->_coreRegistry->registry('current_account')->getAddressfile();
    }
    public function getCompanyFileHref(){
    	return $this->getFileSrc().$this->_coreRegistry->registry('current_account')->getCompanyfile();
    }
    
    public Function getFileSrc()
    {
    	$url = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'affiliate/document/'.$this->customerSession->getCustomerId().'/';
    	return $url;
    }
    public function getAffiliateData(){
    	return $this->_coreRegistry->registry('current_account');
    }
    public function getIdentityType(){
    	
    	return $this->_identityType->toOptionArray();
    }
}
