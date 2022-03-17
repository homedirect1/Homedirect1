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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Controller\Adminhtml\Status;

class StatusInfo extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultPageFactory;
	/**
	 * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Framework\View\Result\PageFactory
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Framework\View\Result\PageFactory $resultPageFactory
			) {
				$this->resultPageFactory = $resultPageFactory;
				parent::__construct($context);
	}
	/*
	 * Create Page
	 * */
	public function execute()
	{
		/** @var \Magento\Backend\Model\View\Result\Page $resultPage */
		return $this->resultPageFactory->create();
	}
}