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
 * @package     Ced_ReferralSystem
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */
namespace Ced\ReferralSystem\Controller\Payout;

class Index extends \Magento\Framework\App\Action\Action {

	public function __construct(
		\Magento\Framework\View\Result\PageFactory $resultPageFactory, 
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Framework\App\Action\Context $context,  
		array $data = []
	) {
		$this->customerSession = $customerSession;
		$this->resultPageFactory = $resultPageFactory;
		parent::__construct ( $context, $data );
	}
	public function execute() {
		if (! $this->customerSession->isLoggedIn ()) {
			$this->messageManager->addErrorMessage ( __ ( 'Please login first' ) );
			return $this->_redirect ( 'customer/account/login' );
		}

		$resultPage = $this->resultPageFactory->create ();
		return $resultPage;
	}
}
