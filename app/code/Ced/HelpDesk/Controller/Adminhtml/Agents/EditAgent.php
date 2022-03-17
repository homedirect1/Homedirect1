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
namespace Ced\HelpDesk\Controller\Adminhtml\Agents;

class EditAgent extends \Magento\Backend\App\Action
{
	/**
	 * @var PageFactory
	 */
	protected $resultForwordFactory;
	/**
	 * @param \Magento\Backend\App\Action\Context
     * @param \Magento\Backend\Model\View\Result\ForwardFactory
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	public function __construct(
			\Magento\Backend\App\Action\Context $context,
			\Magento\Backend\Model\View\Result\ForwardFactory $resultForwordFactory
			) {
				$this->resultForwordFactory = $resultForwordFactory;
				parent::__construct($context);
	}
	/*
	 * forword to newagent controller
	 * */
	public function execute()
	{	
		$resultForward = $this->resultForwordFactory->create();
		return $resultForward->forward('newagent');
	}
}