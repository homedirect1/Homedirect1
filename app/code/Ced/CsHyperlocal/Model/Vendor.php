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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Model;

use Ced\CsMarketplace\Helper\Data;
use Ced\CsMarketplace\Helper\Image;
use Ced\CsMarketplace\Model\VordersFactory;
use Ced\CsMarketplace\Model\VproductsFactory;
use Ced\CsMarketplace\Model\VsettingsFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Magento\Framework\Module\Manager;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Store\Model\WebsiteFactory;
use Ced\CsMarketplace\Model\Url;

/**
 * Class Vendor
 * @package Ced\CsHyperlocal\Model
 */
class Vendor extends \Ced\CsMarketplace\Model\Vendor
{
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    protected $_dataHelper;

    /**
     * Vendor constructor.
     * @param Customer $customer
     * @param Data $dataHelper
     * @param Manager $moduleManager
     * @param MessageManagerInterface $messageManager
     * @param Url $urlModal
     * @param RequestInterface $request
     * @param Image $marketplaceImageHelper
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Ced\CsMarketplace\Helper\Mail $marketplaceMailHelper
     * @param \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper
     * @param \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param \Magento\Framework\Logger\Monolog $logger
     * @param ManagerInterface $eventManager
     * @param VproductsFactory $vProductsFactory
     * @param Attribute $attributeModal
     * @param Vendor\AttributeFactory $marketplaceAttribute
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param VordersFactory $vOrdersFactory
     * @param VsettingsFactory $vSettingsFactory
     * @param System\Config\Source\Paymentmethods $marketplacePaymentMethod
     * @param WebsiteFactory $websiteFactory
     * @param ResourceModel\Vpayment\CollectionFactory $vPaymentCollectionFactory
     * @param UploaderFactory $uploaderFactory
     * @param \Ced\CsMarketplace\Helper\Acl $aclHelper
     * @param \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Product\Url $url
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\Framework\Serialize\Serializer\Json|null $serializer
     * @param \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory
     * @param array $data
     */
    public function __construct(
        Customer $customer,
        Data $dataHelper,
        Manager $moduleManager,
        MessageManagerInterface $messageManager,
        Url $urlModal,
        RequestInterface $request,
        Image $marketplaceImageHelper,
        \Magento\Framework\Filesystem $filesystem,
        \Ced\CsMarketplace\Helper\Mail $marketplaceMailHelper,
        \Magento\UrlRewrite\Helper\UrlRewrite $urlRewriteHelper,
        \Magento\Store\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magento\Framework\Logger\Monolog $logger, ManagerInterface $eventManager,
        \Ced\CsMarketplace\Model\VproductsFactory $vProductsFactory,
        Attribute $attributeModal,
        \Ced\CsMarketplace\Model\Vendor\AttributeFactory $marketplaceAttribute,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Ced\CsMarketplace\Model\VordersFactory $vOrdersFactory,
        \Ced\CsMarketplace\Model\VsettingsFactory $vSettingsFactory,
        \Ced\CsMarketplace\Model\System\Config\Source\Paymentmethods $marketplacePaymentMethod,
        WebsiteFactory $websiteFactory,
        \Ced\CsMarketplace\Model\ResourceModel\Vpayment\CollectionFactory $vPaymentCollectionFactory,
        UploaderFactory $uploaderFactory,
        \Ced\CsMarketplace\Helper\Acl $aclHelper,
        \Magento\UrlRewrite\Model\UrlRewriteFactory $urlRewriteFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Product\Url $url,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Serialize\Serializer\Json $serializer = null,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\CollectionFactory $attributeCollectionFactory,
        array $data = [])
    {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        parent::__construct(
            $customer, $dataHelper, $moduleManager, $messageManager,
            $urlModal, $request, $marketplaceImageHelper, $filesystem,
            $marketplaceMailHelper, $urlRewriteHelper, $storeCollectionFactory,
            $logger, $eventManager, $vProductsFactory, $attributeModal,
            $marketplaceAttribute, $resourceConnection, $vOrdersFactory,
            $vSettingsFactory, $marketplacePaymentMethod, $websiteFactory,
            $vPaymentCollectionFactory, $uploaderFactory, $aclHelper,
            $urlRewriteFactory, $storeManager, $url, $context, $registry,
            $resource, $resourceCollection, $serializer, $data);
    }

    /**
     * Retrieve vendor attributes
     * if $groupId is null - retrieve all vendor attributes
     * @param null $groupId
     * @param bool $skipSuper
     * @param int $storeId
     * @param null $visibility
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributes($groupId = null, $skipSuper = false, $storeId = 0, $visibility = null)
    {
        $vendorAttributes = $this->attributeCollectionFactory->create();
        $typeId = $this->getEntityTypeId();
        if ($groupId) {
            $vendorAttributes->setAttributeGroupFilter($groupId)->load();

            if ($storeId) {
                $vendorAttributes->setStoreId($storeId);
            }
            if ($visibility != null) {
                $vendorAttributes->addFieldToFilter('is_visible', array('gt' => $visibility));
            }
            $vendorAttributes->addFieldToFilter('attribute_code', array('eq' => 'public_name'));

            $this->_eventManager->dispatch(
                'ced_csmarketplace_vendor_group_wise_attributes_load_after',
                ['groupId' => $groupId, 'vendorattributes' => $vendorAttributes]
            );
            $attributes = [];

            foreach ($vendorAttributes as $attribute) {
                if ($attribute->getData('entity_type_id') == $typeId && $attribute->getData('attribute_code') != 'website_id') {
                    if (!$this->_dataHelper->getStoreConfig(\Ced\CsHyperlocal\Helper\Data::MODULE_ENABLE)
                        && (
                            $attribute->getData('attribute_code') == 'location' ||
                            $attribute->getData('attribute_code') == 'latitude' ||
                            $attribute->getData('attribute_code') == 'longitude'
                        )
                    ) {
                        continue;
                    }
                    $attributes[] = $attribute;

                }
            }

        } else {
            $attributes = $vendorAttributes;
        }
        return $attributes;
    }
}
