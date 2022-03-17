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

/**
 * Class PaymentSettingsSave
 * @package Ced\Affiliate\Model\Api\Affiliate
 */
class PaymentSettingsSave implements \Ced\Affiliate\Api\Affiliate\PaymentSettingsSaveInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Ced\Affiliate\Model\PaymentsettingsFactory
     */
    protected $paymentsettingsFactory;

    /**
     * PaymentSettingsSave constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Ced\Affiliate\Model\PaymentsettingsFactory $paymentsettingsFactory
    )
    {
        $this->_logger = $logger;
        $this->paymentsettingsFactory = $paymentsettingsFactory;
    }

    /**
     * @param $parameters
     * @return array|string
     */
    public function savePaymentMethod($parameters)
    {

        $this->_logger->critical(json_encode($parameters));
        if (!isset($parameters['customerId']) && !$parameters['customerId']) {
            $affiliateData['error'] = true;
            $affiliateData['error_message'] = __('No Customer Id');
            return ['data' => $affiliateData];
        }
        $section = 'payment';
        $groups = $parameters['groups'];
        if (strlen($section) > 0 && $parameters['customerId'] && count($groups) > 0) {
            $customer_id = (int)$parameters['customerId'];
            try {
                foreach ($groups as $code => $values) {
                    foreach ($values as $name => $value) {
                        $serialized = 0;
                        $key = strtolower($section . '/' . $code . '/' . $name);
                        if (is_array($value)) {
                            $value = serialize($value);
                            $serialized = 1;
                        }
                        $setting = false;
                        $setting = $this->paymentsettingsFactory->create()
                            ->loadByField(array('key', 'customer_id'), array($key, $customer_id));

                        if ($setting && $setting->getId()) {

                            $setting->setCustomerId($customer_id)
                                ->setGroup($section)
                                ->setKey($key)
                                ->setValue($value)
                                ->setSerialized($serialized)
                                ->save();
                        } else {

                            $setting = $this->paymentsettingsFactory->create();
                            $setting->setCustomerId($customer_id)
                                ->setGroup($section)
                                ->setKey($key)
                                ->setValue($value)
                                ->setSerialized($serialized)
                                ->save();
                        }
                    }
                }

                $affiliateData['success'] = true;
                $affiliateData['success_message'] = __('Successfully Requested');
                return ['data' => $affiliateData];

            } catch (\Exception $e) {
                $affiliateData['error'] = true;
                $affiliateData['error_message'] = $e->getMessage();
                return ['data' => $affiliateData];
            }
        }
    }
}