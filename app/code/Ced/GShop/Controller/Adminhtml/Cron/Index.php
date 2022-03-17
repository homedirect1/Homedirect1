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

namespace Ced\GShop\Controller\Adminhtml\Cron;

use Ced\GShop\Helper\Data;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Ced_GShop::GXpress';
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /** @var Data $dataHelper */
    public $dataHelper;

    /**
     * Index constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param Data $dataHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        Data $dataHelper
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return string
     */

    public function execute()
    {
        $this->messageManager->addNotice($this->dataHelper->getNoticeurl());
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_GShop::GXpress');
        $resultPage->addBreadcrumb(__('GXpress Cron Logs'), __('GXpress Cron Logs'));
        $resultPage->getConfig()->getTitle()->prepend(__('GXpress Cron Logs'));
        return $resultPage;
    }
}
