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
 * @package     Ced_GShop
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Controller\Adminhtml\Log;

use Ced\GShop\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * ResultPageFactory
     * @var PageFactory
     */
    public $resultPageFactory;

    /** @var Data $dataHelper */
    public $dataHelper;

    /**
     * Helper
     * @var PageFactory
     */
    public $helper;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Data $dataHelper
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $dataHelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * Execute
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->messageManager->addNotice($this->dataHelper->getNoticeurl());
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_GShop::Log');
        $resultPage->getConfig()->getTitle()->prepend(__('Activity Log'));
        return $resultPage;
    }
}
