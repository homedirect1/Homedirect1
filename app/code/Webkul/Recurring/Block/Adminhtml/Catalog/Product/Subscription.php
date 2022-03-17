<?php
/**
 * Webkul Software.
 *
 * @category   Webkul
 * @package    Webkul_Recurring
 * @author     Webkul Software Private Limited
 * @copyright  Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license    https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Catalog\Product;

use \Magento\Backend\Block\Template as SubscriptionTemplate;
use \Webkul\Recurring\Model\SubscriptionTypeFactory as plansFactory;
use \Webkul\Recurring\Model\Term as durations;
use \Magento\Directory\Model\CurrencyFactory as currencyFactory;
use Webkul\Recurring\Helper\Data as RecurringHelper;

class Subscription extends SubscriptionTemplate
{
    /**
     * @var string
     */
    protected $_template = "Webkul_Recurring::catalog/product/subscription.phtml";

    /**
     * @var plansFactory
     */
    protected $plansFactory;

    /**
     * @var durations
     */
    protected $durations;

    /**
     * @var currencyFactory
     */
    protected $currencyFactory;
      
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var RecurringHelper
     */
    protected $recurringHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param plansFactory $plansFactory
     * @param durations $durations
     * @param currencyFactory $currencyFactory
     * @param RecurringHelper $recurringHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        plansFactory $plansFactory,
        durations $durations,
        currencyFactory $currencyFactory,
        RecurringHelper $recurringHelper,
        array $data = []
    ) {
        $this->plansFactory     =  $plansFactory;
        $this->durations        =  $durations;
        $this->currencyFactory  =  $currencyFactory;
        $this->request          = $context->getRequest();
        $this->recurringHelper  = $recurringHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get active plans data
     *
     * @return array
     */
    public function getPlansData()
    {
        $durationCollection = $this->durations->getCollection();
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        $durationCollection->addFieldToFilter('status', ['eq' => true])
                           ->setOrder("sort_order", "ASC");
        $returnArray = [];
        $returnArray = [];
        $productId = $this->request->getParam('id');
        $planValue = '';
        $count = 0;
        $symbol = $this->currencyFactory->create()->getCurrencySymbol();
        $availableEngines =  $this->getAvailableEngines();
        foreach ($durationCollection as $durationModel) {
            $collection = $this->plansFactory->create()->getCollection();
            $collection->addFieldToFilter('type', ['eq' => $durationModel->getId() ]);
            $collection->addFieldToFilter('store_id', ['eq' => $storeId ]);
            $returnArray[$count] = [
               'durationId'             => $durationModel->getId(),
               'durationTitle'          => $durationModel->getTitle(),
               'duration'               => $durationModel->getDuration(),
               'store_id'               => 0,
               'entity_id'              => '',
               'name'                   => '',
               'product_id'             => '',
               'initial_fee'            => '',
               'description'            => '',
               'subscription_charge'    => '',
               'status'                 => false,
               'symbol'                 => $symbol,
               'availableEngines'       => $availableEngines,
               'selectedEngine'         => ''
            ];
            
            foreach ($collection as $model) {
                $initialFee = '';
                if ($productId == $model->getProductId()) {
                    $initialFee          = ($model->getInitialFee() >= 0) ? $model->getInitialFee() :  "";
                    $subscriptionCharge  = ($model->getSubscriptionCharge() != 0) ?
                                            $model->getSubscriptionCharge() :  "";
                    $returnArray[$count]['entity_id']             = $model->getId();
                    $returnArray[$count]['name']                  = $model->getName();
                    $returnArray[$count]['product_id']            = $model->getProductId();
                    $returnArray[$count]['store_id']              = ($model->getStoreId())? $model->getStoreId() : 0;
                    $returnArray[$count]['initial_fee']           = $initialFee;
                    $returnArray[$count]['description']           = $model->getDescription();
                    $returnArray[$count]['subscription_charge']   = $subscriptionCharge;
                    $returnArray[$count]['symbol']                = $symbol;
                    $returnArray[$count]['status']                = ($model->getStatus()) ? true :false;
                    $returnArray[$count]['availableEngines']      = $availableEngines;
                    $returnArray[$count]['selectedEngine']        = $model->getEngine();
                }
            }
            $count ++;
        }
        return $returnArray;
    }

    /**
     * Get payment methods
     *
     * @return array
     */
    private function getAvailableEngines()
    {
        return [
            [
               'id' => 'paypal',
               'name' => __("Paypal Express")
            ]
        ];
    }

    /**
     * Get Recurring Helper
     *
     * @return object \Webkul\Recurring\Helper\Data
     */
    public function getRecurringHelper()
    {
        return $this->recurringHelper;
    }
}
