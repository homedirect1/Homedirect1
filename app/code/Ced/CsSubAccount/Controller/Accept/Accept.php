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
 * @package     Ced_CsSubAccount
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSubAccount\Controller\Accept;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\Session;

/**
 * Class Accept
 * @package Ced\CsSubAccount\Controller\Accept
 */
class Accept extends \Magento\Framework\App\Action\Action
{

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var \Ced\CsSubAccount\Model\AccountStatusFactory
     */
    protected $accountStatusFactory;

    /**
     * Accept constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        \Ced\CsSubAccount\Model\AccountStatusFactory $accountStatusFactory
    )
    {
        parent::__construct($context);
        $this->_customerSession = $customerSession;
        $this->accountStatusFactory = $accountStatusFactory;
    }

    /**
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        $msg = '';
        $email = $this->accountStatusFactory->create()->load($this->getRequest()->getParam('cid'))->getData();
        if (!count($email)) {
            $msg = 'Your Sub-vendor account is deleted.';
        } else {
            if ($email['status'] == 1)
                $msg = 'You are already associated with another seller.';
            if ($email['status'] == 2)
                $msg = 'You have already rejected the seller request.';
        }
        if ($msg) {
            $this->messageManager->addErrorMessage(__($msg));
            $this->_redirect("/");
            return;
        }
        $this->_customerSession->setRequestId($this->getRequest()->getParam('cid'));
        $this->_customerSession->setParentVendor($this->getRequest()->getParam('vid'));
        $this->messageManager->addSuccessMessage(__('You have accepted the seller request. Now Create your seller account'));
        $this->_redirect("cssubaccount/customer/create");
        return;
    }
}
