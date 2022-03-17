<?php

namespace Ced\Rewardsystem\Block\Adminhtml\Sales\Order\Invoice;

use Magento\Framework\View\Element\Template\Context;

class Reward extends \Magento\Framework\View\Element\Template
{
    protected $currency;

    public function __construct(Context $context, array $data = [])
    {
        parent::__construct($context, $data);
    }

    public function initTotals()
    {
        $rewardDiscount = $this->getInvoice()->getInvoicedPoint();
        if ($rewardDiscount) {
            $this->getParentBlock()->addTotalBefore(
                new \Magento\Framework\DataObject(
                    [
                        'code' => 'rewarddiscount',
                        'strong' => false,
                        'label' => 'Reward Discount',
                        'value' => -$rewardDiscount,
                    ]
                ),
                'discount'
            );
        }
        return $this;
    }

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }
}
