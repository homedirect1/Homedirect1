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
 * @package     Ced_Rewardsystem
 * @author   	 CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Rewardsystem\Controller\Adminhtml\Reward;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Customerreport extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Index action
     *
     * @return void
     */
    public function execute()
    {

        //Call page factory to render layout and page content
        $resultPage = $this->resultPageFactory->create();

        //Set the menu which will be active for this page
        $resultPage->setActiveMenu('Ced_Rewardsystem::reward_manage');

        //Set the header title of grid
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reward Report'));

        //Add bread crumb
        $resultPage->addBreadcrumb(__('Ced'), __('Ced'));
        $resultPage->addBreadcrumb(__('Reward System'), __('Reward System'));

        return $resultPage;
    }

    /*
	 * Check permission via ACL resource
	 */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Ced_Rewardsystem::rewardsystem_content');
    }
}
