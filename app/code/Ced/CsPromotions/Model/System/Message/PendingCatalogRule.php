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

namespace Ced\CsPromotions\Model\System\Message;

/**
 * Class PendingCatalogRule
 * @package Ced\CsPromotions\Model\System\Message
 */
class PendingCatalogRule implements \Magento\Framework\Notification\MessageInterface
{

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * PendingCatalogRule constructor.
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     */
    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
    )
    {
        $this->_urlBuilder = $urlBuilder;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        // Retrieve unique message identity
        return md5('PENDING_CATALOG_RULE');
    }

    /**
     * @return bool
     */
    public function isDisplayed()
    {
        // Return true to show your message, false to hide it
        if (count($this->ruleCollectionFactory->create()
            ->addFieldToFilter('is_approve', 0)
            ->addFieldToFilter('vendor_id', ['neq' => 0])
            ->getAllIds()
        )
        ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return string
     */
    public function getText()
    {
        // Retrieve message text
        return '<b>' . count($this->ruleCollectionFactory->create()
                ->addFieldToFilter('is_approve', 0)
                ->addFieldToFilter('vendor_id', ['neq' => 0])
                ->getAllIds()
            ) . '</b>' . __(' Approval Request for Vendor Catalog Rule.' . __(' Approve Vendor Catalog Rule from') . '<a href="' . $this->_urlBuilder->getUrl('catalog_rule/promo_catalog/index') . '">' . __(' Vendor Catalog Rule') . '</a>');
    }

    /**
     * @return int
     */
    public function getSeverity()
    {
        // Possible values: SEVERITY_CRITICAL, SEVERITY_MAJOR, SEVERITY_MINOR, SEVERITY_NOTICE
        return self::SEVERITY_NOTICE;
    }
}