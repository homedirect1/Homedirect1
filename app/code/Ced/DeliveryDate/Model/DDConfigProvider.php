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
 * @package     Ced_DeliveryDate
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\DeliveryDate\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class DDConfigProvider
 * @package Ced\DeliveryDate\Model
 */
class DDConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $json;

    /**
     * @var Config\DateFormat
     */
    protected $dateFormat;

    /**
     * @var Config\Timestamp
     */
    protected $timestamp;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * DDConfigProvider constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Serialize\Serializer\Json $json
     * @param Config\DateFormat $dateFormat
     * @param Config\Timestamp $timestamp
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \Ced\DeliveryDate\Model\Config\DateFormat $dateFormat,
        \Ced\DeliveryDate\Model\Config\Timestamp $timestamp,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->storeManager = $storeManager;
        $this->json = $json;
        $this->dateFormat = $dateFormat;
        $this->timestamp = $timestamp;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     * prepare data and save it to delivery_date for fetching data in delivery-date-block.js
     *
     **/
    public function getConfig()
    {
        $path = 'deliverydate/deliverydate_general';
        $moduleStatus = $this->scopeConfig->getValue($path);

        /*first if to check module is enabled or not*/
        $moduleEnable = !empty($moduleStatus['deliverydate_config']) ?: 0;
        unset($moduleStatus['deliverydate_config']);

        if ($moduleEnable && !empty($moduleStatus) && is_array($moduleStatus)) {
            foreach ($moduleStatus as $key => &$value) {
                switch ($key) {
                    case 'dateFormat':
                        $value = $this->cedDateTimeFromat($value);
                        break;
                    case 'timestamp':
                        $timestamp = [];
                        if (!empty($value)) {
                            $val = class_exists(
                                "\\Magento\\Framework\\Serialize\\Serializer\\Json"
                            ) ? $this->json->unserialize($value) : unserialize($value);

                            if (is_array($val)) {
                                foreach ($val as $k => $values) {
                                    $timestamp[] = (string)(
                                        $values['startTime'] . ':00 to ' . $values['endTime'] . ':00'
                                    );
                                }
                            }
                        }
                        $value = $timestamp;
                        break;
                }

            }
        }
        $moduleStatus['deliveryDateConfig'] = $moduleEnable;

        $baseurl = $this->storeManager
            ->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
        $config = [
            'shipping' => [
                'delivery_date' => $moduleStatus,
                'baseurl' => $baseurl
            ]
        ];
        return $config;
    }


    /**
     * @param $id
     * @return mixed
     */
    private function cedDateTimeFromat($id)
    {
        $options = $this->dateFormat->toOptionArray();

        return $options[$id];
    }

    /**
     * @param $id
     * @return array
     */
    private function cedTimestamp($id)
    {
        $options = $this->timestamp->toOptionArray();
        $result = [];
        foreach ($id as $key => $value) {
            $result[] = $options[$value]['label'];
        }
        return $result;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getStoreId();
    }
}