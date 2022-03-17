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
 * @package     Ced_CsPromotions
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsPromotions\Model\Indexer;

use Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory as RuleCollectionFactory;
use Magento\CatalogRule\Model\Rule;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class IndexBuilder
 * @package Ced\CsPromotions\Model\Indexer
 */
class IndexBuilder extends \Magento\CatalogRule\Model\Indexer\IndexBuilder
{
    const SECONDS_IN_DAY = 86400;

    /**
     * CatalogRuleGroupWebsite columns list
     *
     * This array contain list of CatalogRuleGroupWebsite table columns
     *
     * @var array
     */
    protected $_catalogRuleGroupWebsiteColumnsList = ['rule_id', 'customer_group_id', 'website_id'];

    /**
     * @var int
     */
    protected $batchCount;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory
     */
    protected $vproductsCollectionFactory;

    /**
     * IndexBuilder constructor.
     * @param \Magento\Customer\Model\Session $session
     * @param \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory
     * @param RuleCollectionFactory $ruleCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\Stdlib\DateTime $dateFormat
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param int $batchCount
     */
    public function __construct(
        \Magento\Customer\Model\Session $session,
        \Ced\CsMarketplace\Model\ResourceModel\Vproducts\CollectionFactory $vproductsCollectionFactory,
        RuleCollectionFactory $ruleCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\Stdlib\DateTime $dateFormat,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        $batchCount = 1000
    )
    {
        $this->connection = $resource->getConnection();
        $this->batchCount = $batchCount;
        $this->session = $session;
        $this->vproductsCollectionFactory = $vproductsCollectionFactory;

        parent::__construct(
            $ruleCollectionFactory,
            $priceCurrency,
            $resource,
            $storeManager,
            $logger,
            $eavConfig,
            $dateFormat,
            $dateTime,
            $productFactory,
            $batchCount
        );
    }

    /**
     * Get vendor ID
     *
     * @return int
     */
    public function _getSession()
    {
        return $this->session;
    }

    /**
     * @param Rule $rule
     * @return $this|\Magento\CatalogRule\Model\Indexer\IndexBuilder
     */
    protected function updateRuleProductData(Rule $rule)
    {
        $ruleId = $rule->getId();
        if ($rule->getProductsFilter()) {
            $this->connection->delete(
                $this->getTable('catalogrule_product'),
                ['rule_id=?' => $ruleId, 'product_id IN (?)' => $rule->getProductsFilter()]
            );
        } else {
            $this->connection->delete(
                $this->getTable('catalogrule_product'),
                $this->connection->quoteInto('rule_id=?', $ruleId)
            );
        }

        if (!$rule->getIsActive()) {
            return $this;
        }

        $websiteIds = $rule->getWebsiteIds();
        if (!is_array($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }
        if (empty($websiteIds)) {
            return $this;
        }

        \Magento\Framework\Profiler::start('__MATCH_PRODUCTS__');
        $productIds = $rule->getMatchingProductIds();
        \Magento\Framework\Profiler::stop('__MATCH_PRODUCTS__');

        // Custom code for vendors

        if ($rule->getVendorId()) {
            $vendor_id = $rule->getVendorId();
            if (!$this->_getSession()->getvendorId()) {
                $other_vendor_products = $this->vproductsCollectionFactory->create()
                    ->addFieldToFilter(
                        'vendor_id',
                        array(
                            'eq' => $vendor_id
                        )
                    )
                    ->addFieldToFilter('check_status', array('eq' => \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS))
                    ->addFieldToSelect('product_id');
                $new_array = array();
                foreach ($other_vendor_products->getData() as $single_product) {
                    if (isset($productIds[$single_product['product_id']]))
                        $new_array[$single_product['product_id']] = $productIds[$single_product['product_id']];
                }
                $productIds = $new_array;
            } else {
                $vendor_id = $rule->getVendorId();
                if ($this->_getSession()->getvendorId() == $vendor_id) {
                    $other_vendor_products = $this->vproductsCollectionFactory->create()
                        ->addFieldToFilter(
                            'vendor_id',
                            array(
                                'eq' => $vendor_id
                            )
                        )
                        ->addFieldToFilter('check_status', array('eq' => \Ced\CsMarketplace\Model\Vproducts::APPROVED_STATUS))
                        ->addFieldToSelect('product_id');
                    $new_array = array();
                    foreach ($other_vendor_products->getData() as $single_product) {
                        if (isset($productIds[$single_product['product_id']]))
                            $new_array[$single_product['product_id']] = $productIds[$single_product['product_id']];
                    }
                    $productIds = $new_array;
                } else {
                    $productIds = array();
                }
            }
        }

        $customerGroupIds = $rule->getCustomerGroupIds();
        $fromTime = strtotime($rule->getFromDate());
        $toTime = strtotime($rule->getToDate());
        $toTime = $toTime ? $toTime + self::SECONDS_IN_DAY - 1 : 0;
        $sortOrder = (int)$rule->getSortOrder();
        $actionOperator = $rule->getSimpleAction();
        $actionAmount = $rule->getDiscountAmount();
        $subActionOperator = $rule->getSubIsEnable() ? $rule->getSubSimpleAction() : '';
        $subActionAmount = $rule->getSubDiscountAmount();
        $actionStop = $rule->getStopRulesProcessing();

        $rows = [];

        foreach ($productIds as $productId => $validationByWebsite) {
            foreach ($websiteIds as $websiteId) {
                if (empty($validationByWebsite[$websiteId])) {
                    continue;
                }
                foreach ($customerGroupIds as $customerGroupId) {
                    $rows[] = [
                        'rule_id' => $ruleId,
                        'from_time' => $fromTime,
                        'to_time' => $toTime,
                        'website_id' => $websiteId,
                        'customer_group_id' => $customerGroupId,
                        'product_id' => $productId,
                        'action_operator' => $actionOperator,
                        'action_amount' => $actionAmount,
                        'action_stop' => $actionStop,
                        'sort_order' => $sortOrder,
                    ];

                    if (count($rows) == $this->batchCount) {
                        $this->connection->insertMultiple($this->getTable('catalogrule_product'), $rows);
                        $rows = [];
                    }
                }
            }
        }
        if (!empty($rows)) {
            $this->connection->insertMultiple($this->getTable('catalogrule_product'), $rows);
        }

        return $this;
    }

}
