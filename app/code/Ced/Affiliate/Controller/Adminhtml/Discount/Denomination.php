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
namespace Ced\Affiliate\Controller\Adminhtml\Discount;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Denomination extends \Magento\Backend\App\Action {
	protected $resultPageFactory;
	
	public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory) {
		parent::__construct ( $context );
		$this->resultPageFactory = $resultPageFactory;
	}

	public function execute() {
		$resultPage = $this->resultPageFactory->create ();
		$resultPage->getConfig ()->getTitle ()->prepend ( __ ( 'Denomination Rules'));
        return $resultPage;
    }
}