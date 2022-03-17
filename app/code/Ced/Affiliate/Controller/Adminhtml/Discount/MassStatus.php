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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Ced\Affiliate\Model\ResourceModel\DiscountDenomination\CollectionFactory;

/**
 * Class MassStatus
 * @package Ced\Affiliate\Controller\Adminhtml\Discount
 */
class MassStatus extends AbstractMassAction
{
    /**
     * @var Filter
     */
    protected $filter;
 
    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \Ced\Affiliate\Model\DiscountDenominationFactory
     */
    protected $discountDenominationFactory;

    /**
     * MassStatus constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
     */
    public function __construct(
       Context $context,
       Filter $filter,
       CollectionFactory $collectionFactory,
        \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
   ) {
       parent::__construct($context, $filter);
       $this->collectionFactory = $collectionFactory;
       $this->discountDenominationFactory = $discountDenominationFactory;
   }
    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
  
    protected function massAction(AbstractCollection $collection)
    {
    	$changeStatus = 0;
    	foreach ($collection->getAllIds() as $_accountId) {
    		$_account = $this->discountDenominationFactory->create()->load ($_accountId);
    		$_account->setStatus($this->getRequest()->getParam('status'));
    		$_account->setApprove($this->getRequest()->getParam('status'));
    		$_account->save();
    		$changeStatus++;
    	}
    
    	if ($changeStatus) {
    		$this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $changeStatus));
    	}
    	/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    	$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
    	$resultRedirect->setPath('affiliate/discount/denomination');
    
    	return $resultRedirect;
    }
}