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

namespace Ced\Affiliate\Model\Source\Config;

/**
 * Class Paymentmethods
 * @package Ced\Affiliate\Model\Source\Config
 */
class Paymentmethods extends AbstractBlock
{

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Paymentmethods constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
     * @param Escaper|null $escaper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory $attrOptionCollectionFactory,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\OptionFactory $attrOptionFactory
    )
    {
        parent::__construct($attrOptionCollectionFactory, $attrOptionFactory);
        $this->scopeConfig = $scopeConfig;
    }

    const XML_PATH_CED_AFFILIATE_CUSTOMER_PAYMENT_METHODS = 'affiliate/customer/payment_methods';

    /**
     * Retrieve Option values array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $payment_methods = $this->scopeConfig->getValue(self::XML_PATH_CED_AFFILIATE_CUSTOMER_PAYMENT_METHODS);
        $payment_methods = array_keys((array)$payment_methods);
        $options = array();
        foreach ($payment_methods as $payment_method) {
            $payment_method = strtolower(trim($payment_method));
            $options[] = array('value' => $payment_method, 'label' => __(ucfirst($payment_method)));
        }
        return $options;
    }

}
