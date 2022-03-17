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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Controller\Adminhtml\Zipcode;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

/**
 * Class Import
 * @package Ced\CsHyperlocal\Controller\Adminhtml\Zipcode
 */
class Import extends Action
{
    /**
     * Import constructor.
     * @param Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(Context $context, \Magento\Framework\View\Result\PageFactory $resultPageFactory)
    {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();

        $resultPage->setActiveMenu('Ced_CsHyperlocal::shippingarea');

        $resultPage->getConfig()->getTitle()->prepend(__('Import Zipcode CSV'));

        return $resultPage;


    }

    protected function _isAllowed()

    {
        return $this->_authorization->isAllowed('Ced_CsHyperlocal::shippingarea');

    }
}
