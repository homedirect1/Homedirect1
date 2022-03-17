<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul Software Private Limited
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\Data\Form\FormKey\Validator as FormKeyValidator;

abstract class AbstractRecurring extends Action
{
    const ENABLE = true;
    const DISABLE = false;

    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Webkul_Recurring::recurring';

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    
    /**
     * @var \Webkul\Recurring\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Webkul\Recurring\Model\Subscription
     */
    protected $plans;

    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var \Webkul\Recurring\Helper\Stripe
     */
    protected $stripeHelper;

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $massFilter;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Webkul\Recurring\Helper\Paypal
     */
    protected $paypalHelper;

    /**
     *
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Webkul\Recurring\Helper\Data $helper
     * @param \Webkul\Recurring\Helper\Paypal $paypalHelper
     * @param \Magento\Framework\Registry $registry
     * @param BackendSession $backendSession
     * @param \Webkul\Recurring\Model\SubscriptionType $plans
     * @param \Webkul\Recurring\Model\Term $terms
     * @param \Webkul\Recurring\Helper\Stripe $stripeHelper
     * @param \Webkul\Recurring\Model\Subscriptions $subscriptions
     * @param \Magento\Catalog\Model\Product $product
     * @param FormKeyValidator $formKeyValidator
     * @param \Magento\Ui\Component\MassAction\Filter $massFilter
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Webkul\Recurring\Helper\Data $helper,
        \Webkul\Recurring\Helper\Paypal $paypalHelper,
        \Webkul\Recurring\Helper\Stripe $stripeHelper,
        \Magento\Framework\Registry $registry,
        BackendSession $backendSession,
        \Webkul\Recurring\Model\SubscriptionType $plans,
        \Webkul\Recurring\Model\Term $terms,
        \Webkul\Recurring\Model\Subscriptions $subscriptions,
        \Magento\Catalog\Model\Product $product,
        FormKeyValidator $formKeyValidator,
        \Magento\Ui\Component\MassAction\Filter $massFilter
    ) {
        $this->paypalHelper       = $paypalHelper;
        $this->stripeHelper       = $stripeHelper;
        $this->helper             = $helper;
        $this->registry           = $registry;
        $this->plans              = $plans;
        $this->terms              = $terms;
        $this->subscriptions      = $subscriptions;
        $this->product            = $product;
        $this->backendSession     = $backendSession;
        $this->formKeyValidator   = $formKeyValidator;
        $this->massFilter         = $massFilter;
        $this->resultPageFactory  = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Set status
     *
     * @param object $model
     * @param boolean $status
     * @return void
     */
    public function setStatus($model, $status)
    {
        $model->setStatus($status)->setId($model->getId())->save();
    }
}
