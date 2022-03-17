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
 * @package     Ced_GoogleMap
 * @author 	    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\GoogleMap\Plugin\Block\Checkout;


class LayoutProcessor
{

    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        $paymentForms = $jsLayout['components']['checkout']['children']['steps']['children']
        ['billing-step']['children']['payment']['children']
        ['payments-list']['children'];


        $paymentMethodForms = array_keys($paymentForms);
        if (!isset($paymentMethodForms)) {
            return $jsLayout;
        }

        foreach ($paymentMethodForms as $paymentMethodForm) {
            $paymentMethodCode = str_replace('-form', '', $paymentMethodForm, $paymentMethodCode);
            $jsLayout = $this->filedBillingAddressType($jsLayout, $paymentMethodForm, $paymentMethodCode);
        }

        $jsLayout = $this->filedShippingAddressType($jsLayout);
        return $jsLayout;
    }

    protected function filedBillingAddressType($jsLayout, $paymentMethodForm, $paymentMethodCode) {
        foreach (['latitude' => 'latitude', 'longitude' => 'longitude'] as $key => $attribute) {
            $customField = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'billingAddress' . $paymentMethodCode . '.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'id' => $attribute
                ],
                'dataScope' => 'billingAddress' . $paymentMethodCode . '.custom_attributes.' . $attribute,
                'label' => ucfirst($attribute),
                'provider' => 'checkoutProvider',
                'sortOrder' => 80,
                'readonly' => true,
                'visible' => true,
                'value' => 0,
                'id' => $attribute
            ];

            $jsLayout['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list']['children'][$paymentMethodForm]
            ['children']['form-fields']['children'][$attribute] = $customField;
        }

        return $jsLayout;
    }

    protected function filedShippingAddressType($jsLayout) {
        foreach (['latitude', 'longitude'] as $attribute) {
            $customField = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    // customScope is used to group elements within a single form (e.g. they can be validated separately)
                    'customScope' => 'shippingAddress.custom_attributes',
                    'customEntry' => null,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'id' => $attribute
                ],
                'dataScope' => 'shippingAddress.custom_attributes' . '.' . $attribute,
                'label' => ucfirst($attribute),
                'provider' => 'checkoutProvider',
                'sortOrder' => 80,
                'visible' => true,
                //'disabled' => true,
                'value' => 0,
                'id' => $attribute
            ];

            $jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children'][$attribute] = $customField;
        }

        return $jsLayout;
    }
}
