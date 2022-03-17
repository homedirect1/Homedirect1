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

use Magento\Backend\App\Action;

/**
 * Class Edit
 * @package Ced\Affiliate\Controller\Adminhtml\Discount
 */
class Edit extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Ced\Affiliate\Model\DiscountDenominationFactory
     */
    protected $discountDenominationFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Ced\Affiliate\Model\DiscountDenominationFactory $discountDenominationFactory
    )
    {

        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        $this->discountDenominationFactory = $discountDenominationFactory;
        parent::__construct($context);
    }

    /**
     *
     * {@inheritdoc}
     *
     */
    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Init actions
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }

    /**
     * Edit grid record
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam("id");
        $model = $this->discountDenominationFactory->create();

        if ($id) {
            $model->load($id);
        }
        $this->_coreRegistry->register('discount_form_data', $model);

        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb($id ? __('Edit Discount Denomination Rule') : __('New Rule'), $id ? __('Edit Discount Denomination Rule') : __('New Rule'));
        $resultPage->getConfig()->getTitle()->prepend(__('Rules'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('Add Discount Denomination Rule'));
        return $resultPage;
    }
}