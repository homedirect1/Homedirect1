<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
  * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Controller\Adminhtml\Profile;

use Ced\GShop\Model\AccountsFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Edit
 * @package Ced\GShop\Controller\Adminhtml\Profile
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var
     */
    protected $_entityTypeId;

    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Ced\GShop\Helper\MultiAccount
     */
    protected $multiAccountHelper;

    /**
     * @var \Ced\GShop\Helper\Data
     */
    protected $dataHelper;
    /** @var mixed $scopeConfigManager */
    protected $scopeConfigManager;
    /** @var AccountsFactory $accountsFactory */
    protected $accountsFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        PageFactory $resultPageFactory,
        \Ced\GShop\Helper\MultiAccount $multiAccountHelper,
        \Ced\GShop\Model\AccountsFactory $accountsFactory,
        \Ced\GShop\Helper\Data $dataHelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->multiAccountHelper = $multiAccountHelper;
        $this->dataHelper = $dataHelper;
        $this->accountsFactory = $accountsFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->scopeConfigManager = $this->_objectManager->get('Magento\Framework\App\Config\ScopeConfigInterface');
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $accountId = $this->getRequest()->getParam('account_id');
        if(!$accountId) {
            $accountId = $this->scopeConfigManager->getValue('gshop_config/gshop_setting/primary_account');
        }
        $account = $this->accountsFactory->create()->loadByField('id', $accountId);
        $accName = $account->getAccountCode();
        $profileCode = $this->getRequest()->getParam('pcode');
        if (empty($account->getAccountToken()) && empty($profileCode)) {
            $this->messageManager->addErrorMessage("Fetch Token for selected Account $accName before creating Profile");
            return $this->_redirect('*/*/index');
        }

        if ($profileCode) {
            $profile = $this->_objectManager->create('Ced\GShop\Model\Profile')->getCollection()->addFieldToFilter('profile_code', $profileCode)->getFirstItem();
        } else {
            $profile = $this->_objectManager->create('Ced\GShop\Model\Profile');
        }
        $this->getRequest()->setParam('is_profile', 1);
        $this->_coreRegistry->register('current_profile', $profile);
        $this->multiAccountHelper->getAccountRegistry($profile->getAccountId());
        $this->dataHelper->updateAccountVariable();

        $item = $profile->getId() ? $profile->getProfileName() : __('New Profile');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend($profile->getId() ? $profile->getProfileName() : __('New Profile'));
        $resultPage->getLayout()->getBlock('profile_edit_js')->setIsPopup((bool)$this->getRequest()->getParam('popup'));
        return $resultPage;
    }
}
