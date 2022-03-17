<?php

namespace Ced\Rewardsystem\Block\Reward;

use Magento\Store\Model\ScopeInterface;

/**
 * Class Points
 * @package Ced\Rewardsystem\Block\Reward
 */
class Points extends \Magento\Framework\View\Element\Template
{
    protected $_customerSession;

    private $customerId = null;
    /**
     * @var \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory
     */
    protected $_pointCollectionFactory;
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        array $data = [],
        \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\CollectionFactory $pointCollectionFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->date = $date;
        $this->_storeManager = $context->getStoreManager();
        $this->_scopeConfig = $context->getScopeConfig();
        $this->priceCurrency = $priceCurrency;
        $this->_pointCollectionFactory = $pointCollectionFactory;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    /**
     * @return int|null
     */
    public function getCustomerId()
    {
        if ($this->customerId === null) {
            $this->customerId = $this->_customerSession->getCustomerId();
        }
        return $this->customerId;
    }

    /**
     * @return \Ced\Rewardsystem\Model\ResourceModel\Regisuserpoint\Collection
     */
    public function getCompletedPoints()
    {
        $customerId = $this->getCustomerId();

        $collection = $this->_pointCollectionFactory->create()
            ->addFieldToFilter('main_table.customer_id', $customerId)
            ->addFieldToFilter('main_table.status', 'complete')
            ->addFieldToFilter('point', ['gt' => 0]);

        return $collection;
    }

    public function getUsedPoints()
    {
        $customerId=$this->getCustomerId();
        $collection = $this->_pointCollectionFactory->create()
            ->addFieldToFilter('main_table.customer_id', $customerId)
            ->addFieldToFilter('point_used', ['gt' => 0]);
        return $collection;
    }

    /**
     * @return int|mixed
     */
    public function subtotal()
    {
        $date = $this->date->gmtDate();
        $curdate = strtotime($date);
        $collection = $this->getCompletedPoints();
        $totalpoint = $collection->getData();
        $subtotal = 0;

        foreach ($totalpoint as $key => $value) {
            $mydate = strtotime($value['expiration_date']);
            if ($curdate <= $mydate || !isset($value['expiration_date'])) {
                $subtotal = $subtotal + $value['point'];
            }
        }

        $usedPointCollection = $this->getUsedPoints();
        $totalremainingpoint = $usedPointCollection->getData();
        $usedpoint = 0;
        foreach ($totalremainingpoint as $key => $value) {
            $usedpoint = $usedpoint + $value['point_used'];
        }

        return $subtotal - $usedpoint;
    }

    /**
     * @return float
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPointsValue()
    {
        $currencyCode = $this->_storeManager->getStore(null)->getCurrentCurrencyCode();

        $store = $this->_scopeConfig;
        // $points = $store->getValue('reward/setting/point');
        $pointsPrice = $store->getValue('reward/setting/point_value', ScopeInterface::SCOPE_STORE);

        $PointinPrice = $this->priceCurrency->format($pointsPrice, false, 2, null, $currencyCode);

        return $PointinPrice;
    }
}
