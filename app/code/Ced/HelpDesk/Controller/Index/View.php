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

namespace Ced\HelpDesk\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class View
 * @package Ced\HelpDesk\Controller\Index
 */
class View extends \Magento\Framework\App\Action\Action
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
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Ced\HelpDesk\Helper\Data $helpdeskHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Ced\HelpDesk\Helper\Data $helpdeskHelper
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->helpdeskHelper = $helpdeskHelper;
        parent::__construct($context);
    }

    /**
     * Create page
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page|void
     */
    public function execute()
    {
        if (!$this->helpdeskHelper->getStoreConfig('helpdesk/general/enable')) {
            $this->_redirect('cms/index/index');
            return;
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('View Ticket Status'));
        return $resultPage;
    }
}