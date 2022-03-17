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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\ReferralSystem\Controller\Summary;

class Lists extends \Ced\Affiliate\Controller\Affiliate {
	
	public function execute() {
		/* if (! $this->_getSession->isLoggedIn ()) {
			$this->messageManager->addError ( __ ( 'Please login first' ) );
			$this->_redirect ( 'customer/account/login' );
			return;
		} */
		$resultPage = $this->resultPageFactory->create ();
		return $resultPage;
	}
}