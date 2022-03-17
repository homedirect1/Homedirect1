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
 * @package     Ced_CsDeliveryDate
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsDeliveryDate\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Serialize\Serializer\Serialize;

/**
 * Class DDConfigProvider
 * @package Ced\CsDeliveryDate\Model
 */
class DDConfigProvider implements ConfigProviderInterface
{
    /**
     * @var \Ced\CsDeliveryDate\Helper\ConfigData
     */
    protected $configDataHelper;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory
     */
    protected $vsettingsCollectionFactory;

    /**
     * @var Serialize
     */
    protected $jsonSerializer;

    /**
     * DDConfigProvider constructor.
     * @param \Ced\CsDeliveryDate\Helper\ConfigData $configDataHelper
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollectionFactory
     * @param Serialize $jsonSerializer
     */
    public function __construct(
        \Ced\CsDeliveryDate\Helper\ConfigData $configDataHelper,
        \Ced\CsMarketplace\Model\ResourceModel\Vsettings\CollectionFactory $vsettingsCollectionFactory,
        Serialize $jsonSerializer
    )
    {
        $this->configDataHelper = $configDataHelper;
        $this->vsettingsCollectionFactory = $vsettingsCollectionFactory;
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * {@inheritdoc}
     * prepare data and save it to delivery_date for fetching data in delivery-date-block.js
     *
     **/
    public function getConfig()
    {
        $enableModule = ($this->configDataHelper->moduleEnabled() == 1);
        $multishippingHelper = $this->configDataHelper->getMultishippingHelper();
        $multishippingEnable = ($multishippingHelper->isEnabled() == 1);
        if ($enableModule && $multishippingEnable) {
            $section = 'csdeliverydate';
            $moduleStatus = $this->vsettingsCollectionFactory->create();

            $settings = $moduleStatus->addFieldToFilter('group', $section)->getData();

            $moduleEnable = 0;
            $vendorId = [];
            $fdata = [];
            /*first if to check module is enabled or not by vendor*/
            foreach ($settings as $key => $value) {
                if (!in_array($value['vendor_id'], $vendorId)) {
                    $vendorId[] = $value['vendor_id'];
                }
            }

            foreach ($vendorId as $vkey => $vvalue) {
                $data = [];
                foreach ($settings as $key => $value) {
                    if ($value['vendor_id'] == $vvalue) {
                        if ($value['key'] == 'csdeliverydate/csdeliverydate/enablesettings') {
                            $moduleEnable = (int)$value['value'];
                            $data = array_merge($data, ['deliveryDateConfig' => $moduleEnable]);
                        }

                        if ($moduleEnable == 1) {
                            $data = array_merge($data, ['vendorId' => $value['vendor_id']]);
                            $key = explode("/", $value['key']);
                            if ($key[2] == 'vddnoteforcalander') {
                                $data = array_merge($data, [$key[2] => $value['value']]);
                            } elseif ($key[2] == 'timestamp') {
                                try {
                                    $val = $this->jsonSerializer->unserialize($value['value']);
                                } catch (\Exception $e) {
                                    $val = [];
                                }
                                $timestamp = [];
                                if (isset($val) && is_array($val)) {
                                    foreach ($val as $tkey => $tvalue) {
                                        $timestamp[] = $tvalue['from'] . ':00' . ' to ' . $tvalue['to'] . ':00';
                                    }
                                }
                                $data = array_merge($data, [$key[2] => $timestamp]);

                            } elseif ($value['serialized'] == 1) {
                                $data = array_merge($data, [$key[2] => $value['value']]);

                            } else {
                                $data = array_merge($data, [$key[2] => $value['value']]);
                            }

                            $fdata[$vvalue] = $data;
                        }
                    }
                }
            }
            $config = [
                'shipping' => [
                    'csdeliverydate' => $fdata,
                    'csDDmoduleEnable' => $enableModule,
                    'multishippingEnable' => $multishippingEnable
                ]
            ];
            return $config;
        }
        $config = [
            'shipping' => [
                'csdeliverydate' => array(),
                'csDDmoduleEnable' => false,
                'multishippingEnable' => $multishippingEnable
            ]
        ];
        return $config;

    }

}
