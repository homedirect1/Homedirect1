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

namespace Ced\Affiliate\Model;

/**
 * Class PaymentMethods
 * @package Ced\Affiliate\Model
 */
class PaymentMethods extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var PaymentsettingsFactory
     */
    protected $paymentsettingsFactory;

    /**
     * @var Source\Config\Paymentmethods
     */
    protected $paymentmethods;

    /**
     * PaymentMethods constructor.
     * @param PaymentsettingsFactory $paymentsettingsFactory
     * @param Source\Config\Paymentmethods $paymentmethods
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory,
        \Ced\Affiliate\Model\Source\Config\Paymentmethods $paymentmethods,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);

        $this->paymentsettingsFactory = $paymentsettingsFactory;
        $this->paymentmethods = $paymentmethods;
    }

    /**
     * @param $customerId
     * @param bool $all
     * @return array
     */
    public function getPaymentMethodsArray($customerId, $all = true)
    {

        $methods = $this->getPaymentMethods();
        $options = array();
        if ($all) {
            $options[''] = __('Select Payment Method');
        }
        if (count($methods) > 0) {
            foreach ($methods as $code => $method) {
                $key = strtolower(\Ced\Affiliate\Model\Paymentsettings::PAYMENT_SECTION . '/' . $method->getCode() . '/active');
                $setting = $this->paymentsettingsFactory->create()
                    ->loadByField(array('key', 'customer_id'), array($key, (int)$customerId));
                if ($setting && $setting->getId() && $setting->getValue()) {
                    $options[$code] = $method->getLabel('label');
                }
            }
        }
        return $options;
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