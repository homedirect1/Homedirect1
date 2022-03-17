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

namespace Ced\Affiliate\Controller\Account;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Context;

/**
 * Class Edit
 * @package Ced\Affiliate\Controller\Account
 */
class Edit extends \Magento\Customer\Controller\AbstractAccount
{
    /** @var CustomerRepositoryInterface */
    protected $customerRepository;

    /** @var DataObjectHelper */
    protected $dataObjectHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @var \Ced\Affiliate\Model\AffiliateAccountFactory
     */
    protected $affiliateAccountFactory;

    /**
     * Edit constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectHelper $dataObjectHelper
     * @param \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        CustomerRepositoryInterface $customerRepository,
        DataObjectHelper $dataObjectHelper,
        \Ced\Affiliate\Model\AffiliateAccountFactory $affiliateAccountFactory
    )
    {
        $this->_coreRegistry = $registry;
        $this->session = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->customerRepository = $customerRepository;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->affiliateAccountFactory = $affiliateAccountFactory;
        parent::__construct($context);
    }

    /**
     * Forgot customer account information page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        $block = $resultPage->getLayout()->getBlock('customer_edit');
        if ($block) {
            $block->setRefererUrl($this->_redirect->getRefererUrl());
        }

        $data = $this->session->getCustomerFormData(true);
        $customerId = $this->session->getCustomerId();
        $customerDataObject = $this->customerRepository->getById($customerId);
        if (!empty($data)) {
            $this->dataObjectHelper->populateWithArray(
                $customerDataObject,
                $data,
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        }
        $this->session->setCustomerData($customerDataObject);
        $this->session->setChangePassword($this->getRequest()->getParam('changepass') == 1);
        $affiliateaccount = $this->affiliateAccountFactory->create();
        $this->_coreRegistry->register('current_account', $affiliateaccount
            ->load($this->session->getCustomer()->getId(), 'customer_id'));

        $resultPage->getConfig()->getTitle()->set(__('Account Information'));
        $resultPage->getLayout()->getBlock('messages')->setEscapeMessageFlag(true);
        return $resultPage;
    }
}
