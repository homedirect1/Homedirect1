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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Controller\Stores;

use Ced\CsStorePickup\Helper\Data;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Search
 * @package Ced\CsStorePickup\Controller\Stores
 */
class Search extends Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * Search constructor.
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
        $this->resultPageFactory = $resultPageFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|Redirect|ResultInterface|Page|void
     */
    public function execute()
    {
        if ($this->dataHelper->isEnable() == "0") {
            $this->_redirect('*/*/index');
            return;
        }
        $data = $this->getRequest()->getPostValue();

        $flag = false;
        $country = trim($data['country_id']);
        $state = trim($data['region_id']);
        $city = trim($data['city']);
        if ($country || $state || $city) {
            $flag = true;
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($flag) {
            $resultRedirect = $this->resultPageFactory->create();
            return $resultRedirect;
        } else {
            $this->messageManager->addErrorMessage('Please enter Country or State or City');
            return $resultRedirect->setPath('*//');
        }
    }
}