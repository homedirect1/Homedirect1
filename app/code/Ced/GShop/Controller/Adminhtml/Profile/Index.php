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
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Controller\Adminhtml\Profile;

use Ced\GShop\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Index
 * @package Ced\GShop\Controller\Adminhtml\Profile
 */
class Index extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';
    /**
     * ResultPageFactory
     * @var PageFactory
     */
    public $resultPageFactory;

    /** @var Data $dataHelper */
    public $dataHelper;

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
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $this->messageManager->addNotice($this->dataHelper->getNoticeurl());
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_GShop::gxpress_profile');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Profile'));
        return $resultPage;
    }
}