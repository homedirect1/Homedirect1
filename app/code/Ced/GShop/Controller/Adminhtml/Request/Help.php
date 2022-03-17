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

namespace Ced\GShop\Controller\Adminhtml\Request;

use Ced\GShop\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Help
 * @package Ced\GShop\Controller\Adminhtml\Request
 */
class Help extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    public $resultPageFactory;

    /** @var Data $dataHelper */
    public $dataHelper;

    /**
     * Help constructor.
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
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /*try {
            $this->_objectManager->create(\Ced\GShop\Cron\AutoDelete::class)->execute();
        } catch (\Exception $exception) {
        }*/

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Ced_GShop::GXpress_knowledge_base');
        $resultPage->getConfig()->getTitle()->prepend(__('Google Shopping Knowledge Base'));
        return $resultPage;
    }

    /**
     * @return bool
     */
    public function _isAllowed()
    {
        $this->messageManager->addNotice($this->dataHelper->getNoticeurl());
        return $this->_authorization->isAllowed('Ced_GShop::GXpress');
    }
}
