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
 * @package     Ced_HelpDesk
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\HelpDesk\Controller\Tickets;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Form
 * @package Ced\HelpDesk\Controller\Tickets
 */
class Form extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ced\HelpDesk\Helper\Data
     */
    protected $helpdeskHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $session;

    /**
     * Form constructor.
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     * @param \Magento\Customer\Model\Session $session
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Ced\HelpDesk\Helper\Data $helpdeskHelper,
        \Magento\Customer\Model\Session $session,
        Context $context,
        PageFactory $resultPageFactory
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->helpdeskHelper = $helpdeskHelper;
        $this->session = $session;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if (!$this->helpdeskHelper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        } else {
            if (!$this->session->isLoggedIn()) {
                $this->messageManager->addErrorMessage(__('Please Login First.'));
                $this->_redirect('customer/account/login');
                return;
            } else {
                $resultPage = $this->resultPageFactory->create();
                $resultPage->getConfig()->getTitle()->set(__('View Support System Tickets'));
            }
        }
        return $resultPage;
    }
}