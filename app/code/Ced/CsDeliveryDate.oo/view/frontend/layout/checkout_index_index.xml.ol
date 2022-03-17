<?xml version="1.0"?>
<!--
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement (EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category    Ced
 * @package     Ced_CsDeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
-->

<page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" layout="checkout" xsi:noNamespaceSchemaLocation="urn:magento:framework:View/Layout/etc/page_configuration.xsd">

    <body>

        <referenceBlock name="deliverydate.minicart" remove="true"/>
        <referenceBlock name="delivery_date" remove="false"/>
        <!-- <referenceBlock name="checkout.root.jsLayout.checkout.steps.shipping-step.shippingAddress.shippingAdditional.delivery_date" remove="true"/> -->

        <referenceBlock name="checkout.root">
            <arguments >
                <argument name="jsLayout" xsi:type="array">
                    <item name="components" xsi:type="array">
                        <item name="checkout" xsi:type="array">
                            <item name="children" xsi:type="array">

                                <item name="sidebar" xsi:type="array">
                                    <item name="children" xsi:type="array">
                                        <item name="summary" xsi:type="array">
                                            <item name="children" xsi:type="array">
                                                <item name="totals" xsi:type="array">
                                                    <item name="children" xsi:type="array">
                                                        <item name="csdeliverydate_summary" xsi:type="array">
                                                            <item name="component" xsi:type="string">Ced_CsDeliveryDate/js/view/delivery-date-summary</item>
                                                            <item name="sortOrder" xsi:type="string">100</item>
                                                            <item name="config" xsi:type="array">
                                                                <item name="template" xsi:type="string">Ced_CsDeliveryDate/delivery-date-summary</item>
                                                            </item>
                                                        </item>
                                                    </item>
                                                </item>
                                            </item>
                                        </item>
                                    </item>
                                </item>

                            </item>
                        </item>
                    </item>
                </argument>
            </arguments>
        </referenceBlock>
    </body>
</page>
