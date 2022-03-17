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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Model\Api\Affiliate;

use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel;

/**
 * Class PaymentSetting
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class PaymentSetting extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Ced\Affiliate\Helper\Data
     */
    protected $affiliateHelper;

    /**
     * @var \Ced\Affiliate\Model\PaymentsettingsFactory
     */
    protected $paymentsettingsFactory;

    /**
     * @var \Ced\Affiliate\Model\Source\Config\Paymentmethods
     */
    protected $paymentmethods;

    /**
     * PaymentSetting constructor.
     * @param \Ced\Affiliate\Helper\Data $affiliateHelper
     * @param \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
     * @param \Ced\Affiliate\Model\Source\Config\Paymentmethods $paymentmethods
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Helper\Data $affiliateHelper,
        \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory,
        \Ced\Affiliate\Model\Source\Config\Paymentmethods $paymentmethods,
        Context $context,
        \Magento\Framework\Registry $registry,
        ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->affiliateHelper = $affiliateHelper;
        $this->paymentsettingsFactory = $paymentsettingsFactory;
        $this->paymentmethods = $paymentmethods;
    }

    /**
     * @param $customerId
     * @return array
     */
    public function getPaymentSettingInformation($customerId)
    {

        if ($customerId == null) {
            $response = array(
                'data' => array(
                    'message' => 'Customer Id Is Empty',
                    'success' => false,
                )
            );
            return $response;
        }

        $cpaymethods = array();
        if ($customerId) {
            $methods = $this->getPaymentMethods();
            if (count($methods) > 0) {
                $cnt = 0;
                $tcnt = 0;
                foreach ($methods as $code => $method) {
                    $fields = $method->getFields();
                    if (count($fields) > 0) {
                        foreach ($fields as $id => $field) {
                            $key = strtolower(\Ced\Affiliate\Model\Paymentsettings::PAYMENT_SECTION . '/' . $method->getCode() . '/' . $id);
                            $value = '';
                            $customer_id = $this->affiliateHelper->getTableKey('customer_id');
                            $key_tmp = $this->affiliateHelper->getTableKey('key');
                            $setting = $this->paymentsettingsFactory->create()
                                ->loadByField(array($key_tmp, $customer_id), array($key, (int)$customerId));
                            if ($setting)
                                $value = $setting->getValue();

                            if ($value == 'null' || $value == '') {
                                $value = '';
                            }
                            $values = isset($field['values']) ? $field['values'] : '0';
                            if (isset($field['type'])) {
                                $type = isset($field['type']) ? $field['type'] : 'text';
                            }
                            if (isset($field['type']) && $field['type'] == 'select') {
                                $type = isset($field['type']) ? $field['type'] : 'select';
                                $availOptins = array();
                                if (isset($field['values']) && (count($field['values']) > 0)) {
                                    foreach ($field['values'] as $key => $value) {
                                        $availOptins[] = array('value' => $key, 'label' => $value);
                                    }
                                }
                                $values = $availOptins;
                            }
                            $cpaymethods['fieldset'][$tcnt][$method->getLabel('label')->getText()][] = array(
                                'label' => $method->getLabel($id),
                                'value' => $setting->getValue(),
                                'values' => $values,
                                'type' => $type,
                                'name' => $method->getCode(),
                                'tag' => $id
                            );
                        }
                    }
                    ++$tcnt;
                }
                ++$cnt;
            }
        }
        $data = array();
        if (count($cpaymethods) > 0) {
            $data = array(
                'data' => $cpaymethods
            );
        }
        return ["data" => $data];
    }

    /**
     * @return array
     */
    public function getPaymentMethods()
    {
        $availableMethods = $this->paymentmethods->toOptionArray();
        $methods = array();
        if (count($availableMethods) > 0) {
            foreach ($availableMethods as $method) {
                if (isset($method['value'])) {
                    $object = \Magento\Framework\App\ObjectManager::getInstance()
                        ->get('Ced\Affiliate\Model\Customer\Payment\Methods\\' . ucfirst($method['value']));
                    if (is_object($object)) {
                        $methods[$method['value']] = $object;
                    }
                }
            }
        }
        return $methods;
    }
}
