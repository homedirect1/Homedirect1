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

namespace Ced\CsStorePickup\Block;

use Ced\StorePickup\Model\StoreHourFactory;
use Ced\StorePickup\Model\StoreInfoFactory;
use Magento\Checkout\Model\Session;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Daytimings
 * @package Ced\CsStorePickup\Block
 */
class Daytimings extends \Magento\Framework\View\Element\Template
{

    /**
     * @var StoreInfoFactory
     */
    protected $_storeInfo;

    /**
     * @var \Ced\CsStorePickup\Model\StoreHourFactory
     */
    protected $_storeTime;

    /**
     * @var UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * Daytimings constructor.
     * @param Context $context
     * @param Session $checkoutSession
     * @param UrlInterface $urlInterface
     * @param StoreHourFactory $storeHourFactory
     * @param StoreInfoFactory $storeInfoFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        UrlInterface $urlInterface,
        StoreHourFactory $storeHourFactory,
        StoreInfoFactory $storeInfoFactory,
        array $data = []
    )
    {
        $this->checkoutSession = $checkoutSession;
        $this->_request = $context->getRequest();

        $this->_storeTime = $storeHourFactory;
        $this->_storeInfo = $storeInfoFactory;

        $this->_urlInterface = $urlInterface;
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
        $storeTimings = $this->_storeTime->create()
            ->getCollection()
            ->addFieldToFilter('pickup_id', $this->getStoreId())
            ->addFieldToFilter('days', $this->getDay())
            ->addFieldToFilter('status', '1')->getData();

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