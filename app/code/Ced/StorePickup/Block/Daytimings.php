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
 * @package   Ced_StorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\StorePickup\Block;

/**
 * Class Daytimings
 * @package Ced\StorePickup\Block
 */
class Daytimings extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Ced\StorePickup\Model\StoreHour
     */
    protected $storeTime;

    /**
     * Daytimings constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Ced\StorePickup\Model\StoreHour $storeTime
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Ced\StorePickup\Model\StoreHour $storeTime,
        array $data = []
    )
    {

        $this->checkoutSession = $checkoutSession;
        $this->_storeTime = $storeTime;
        parent::__construct($context, $data);
        self::getTimings();
    }

    /**
     * @return false|string
     */
    public function getDay()
    {
        $storeDate = $this->getRequest()->getParam('storepkp_date');
        $nameOfDay = date('l', strtotime($storeDate));
        return $nameOfDay;
    }

    /**
     * @return mixed
     */
    public function getStoreId()
    {
        return $this->checkoutSession->getData('storeId');
    }

    /**
     * @return array
     */
    public function getTimings()
    {

        $timings = [];
        $storeTimings = $this->_storeTime->getCollection()->addFieldToFilter('pickup_id', $this->getStoreId())->addFieldToFilter('days', $this->getDay())->addFieldToFilter('status', '1')->getData();

        if (!empty($storeTimings)) {
            foreach ($storeTimings as $value) {
                $timings['opening'] = $value['start'];
                $timings['closing'] = $value['end'];
                $timings['interval'] = $value['interval'];
            }

            return $timings;
        }
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_urlInterface->getBaseUrl();
    }

}
