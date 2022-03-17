<?php

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
 * @package     Ced_GShop
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

if (defined('DS') === false) {
    define('DS', DIRECTORY_SEPARATOR);
}

class UpgradeData implements UpgradeDataInterface
{
    /**
     * directoryList
     * @var directoryList
     */
    public $directoryList;

    /** @var EavSetupFactory $eavSetupFactory */
    private $eavSetupFactory;
    /** @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute */
    private $eavAttribute;

    public $objectManager;

    public $logger;

    /**
     * UpgradeData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $state
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state
    )
    {
        $this->objectManager = $objectManager;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->directoryList = $directoryList;
        $this->eavAttribute = $eavAttribute;
        $this->logger = $logger;
        //if(!$state->getAreaCode()) {
        try {
            $state->setAreaCode('frontend');
        } catch (\Exception $e) {
        }
        //}
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $appPath = $this->directoryList->getRoot();

        $data = $objectManager->create('\Ced\GShop\Helper\Data');
        $gxpressPath = $appPath . DS . "app" . DS . "code" . DS . "Ced" . DS . "GShop" . DS . "Setup" . DS . "GXpressJson" . DS;

        $path = $gxpressPath . "gxpress_category.json";
        $categories = $data->loadFile($path);

        $path = $gxpressPath . "gxpress_attribute.json";
        $attributes = $data->loadFile($path);

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            try {
                $table = $setup->getTable('gxpress_category');
                if ($table) {
                    $setup->getConnection()->truncateTable($table);
                }
                $setup->getConnection()->insertArray(
                    $table,
                    [
                        'id',
                        'csv_firstlevel_id',
                        'csv_secondlevel_id',
                        'csv_thirdlevel_id',
                        'csv_fourthlevel_id',
                        'csv_fifthlevel_id',
                        'csv_sixthlevel_id',
                        'csv_seventhlevel_id',
                        'name',
                        'path',
                        'level',
                        'magento_cat_id',
                        'gxpress_required_attributes',
                        'gxpress_attributes'
                    ],
                    is_array($categories) ? $categories : []
                );
            } catch (\Exception $e) {
            }

            try {
                $table = $setup->getTable('gxpress_attribute');
                if ($table) {
                    $setup->getConnection()->truncateTable($table);
                }
                $setup->getConnection()->insertArray(
                    $table,
                    [
                        "id",
                        "gxpress_attribute_name",
                        "magento_attribute_code",
                        "gxpress_attribute_doc",
                        "is_mapped",
                        "gxpress_attribute_enum",
                        "gxpress_attribute_level",
                        "gxpress_attribute_type",
                        "gxpress_attribute_depends_on",
                        "default_value"
                    ],
                    is_array($attributes) ? $attributes : []
                );
            } catch (\Exception $e) {
            }
        }

        if (version_compare($context->getVersion(), '0.0.3', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $groupName = 'Google';
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
            $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

            if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_product_expires')) {
                $eavSetup->addAttribute(
                    'catalog_product',
                    'google_product_expires',
                    [
                        'group' => 'Google',
                        'note' => ' Product Expire date on Google express',
                        'input' => 'text',
                        'type' => 'varchar',
                        'label' => 'Product Expire on',
                        'backend' => '',
                        'visible' => 1,
                        'required' => 0,
                        'sort_order' => 3,
                        'user_defined' => 1,
                        'comparable' => 0,
                        'visible_on_front' => 0,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                    ]
                );
            }

            try {
                $table = $setup->getTable('gxpress_category');
                if ($table) {
                    $setup->getConnection()->truncateTable($table);
                }
                $setup->getConnection()->insertArray(
                    $table,
                    [
                        'id',
                        'csv_firstlevel_id',
                        'csv_secondlevel_id',
                        'csv_thirdlevel_id',
                        'csv_fourthlevel_id',
                        'csv_fifthlevel_id',
                        'csv_sixthlevel_id',
                        'csv_seventhlevel_id',
                        'name',
                        'path',
                        'level',
                        'magento_cat_id',
                        'gxpress_required_attributes',
                        'gxpress_attributes'
                    ],
                    is_array($categories) ? $categories : []
                );
            } catch (\Exception $e) {
            }

            try {
                $table = $setup->getTable('gxpress_attribute');
                if ($table) {
                    $setup->getConnection()->truncateTable($table);
                }
                $setup->getConnection()->insertArray(
                    $table,
                    [
                        "id",
                        "gxpress_attribute_name",
                        "magento_attribute_code",
                        "gxpress_attribute_doc",
                        "is_mapped",
                        "gxpress_attribute_enum",
                        "gxpress_attribute_level",
                        "gxpress_attribute_type",
                        "gxpress_attribute_depends_on",
                        "default_value"
                    ],
                    is_array($attributes) ? $attributes : []
                );
            } catch (\Exception $e) {
            }
        }
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $groupName = 'Google';
            $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
            $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
            $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
            $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

            if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_category_id')) {
                $eavSetup->addAttribute(
                    'catalog_product',
                    'google_category_id',
                    [
                        'group' => 'Google',
                        'note' => 'Google Category Id',
                        'input' => 'text',
                        'type' => 'varchar',
                        'label' => 'Google Category Id',
                        'backend' => '',
                        'visible' => 1,
                        'required' => 0,
                        'sort_order' => 3,
                        'user_defined' => 1,
                        'comparable' => 0,
                        'visible_on_front' => 0,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                    ]
                );
            }
            if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_status')) {
                $eavSetup->addAttribute(
                    'catalog_product',
                    'google_status',
                    [
                        'group' => 'Google',
                        'note' => 'Google Status',
                        'input' => 'boolean',
                        'type' => 'text',
                        'label' => 'Google Status',
                        'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                        'backend' => '',
                        'visible' => 1,
                        'required' => 0,
                        'sort_order' => 3,
                        'default' => 'Yes',
                        'user_defined' => 1,
                        'comparable' => 0,
                        'visible_on_front' => 0,
                        'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                        'option' => ['values' => ['Yes', 'No']]
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), '1.0.2', '<')) {
            try {
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                $groupName = 'Google';
                $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
                $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
                $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
                $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'google_multipack')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'google_multipack',
                        [
                            'group' => 'Google',
                            'note' => 'Multipack Quantity',
                            'input' => 'text',
                            'type' => 'int',
                            'label' => 'Multipack Quantity',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'google_is_bundle')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'google_is_bundle',
                        [
                            'group' => 'Google',
                            'note' => 'Is Bundle',
                            'input' => 'boolean',
                            'type' => 'text',
                            'label' => 'Is Bundle',
                            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'default' => 'Yes',
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                            'option' => ['values' => ['Yes', 'No']]
                        ]
                    );
                }
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                $groupName = 'Adwords';
                $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
                $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
                $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
                $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_status')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_status',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Status',
                            'input' => 'boolean',
                            'type' => 'text',
                            'label' => 'Adwords Status',
                            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Table',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'default' => 'Yes',
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                            'option' => ['values' => ['Yes', 'No']]
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_name')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_name',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Title',
                            'input' => 'text',
                            'type' => 'varchar',
                            'label' => 'Adwords Title',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                            'frontend_class' => 'validate-length maximum-length-150',
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_description')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_description',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Description',
                            'input' => 'textarea',
                            'type' => 'text',
                            'label' => 'Adwords Description',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                            'frontend_class' => 'validate-length maximum-length-5000',
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_price')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_price',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Price',
                            'input' => 'price',
                            'type' => 'decimal',
                            'label' => 'Adwords Price',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_sale_price')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_sale_price',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Sale Price',
                            'input' => 'price',
                            'type' => 'decimal',
                            'label' => 'Adwords Sale Price',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_sale_price_effective_date')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_sale_price_effective_date',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Sale Price Effective Date',
                            'input' => 'date',
                            'type' => 'datetime',
                            'label' => 'Adwords Sale Price Effective Date',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_product_type')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_product_type',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Product Type',
                            'input' => 'text',
                            'type' => 'varchar',
                            'label' => 'Adwords Product Type',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                        ]
                    );
                }
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_condition')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_condition',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Item Condition',
                            'input' => 'select',
                            'type' => 'text',
                            'label' => 'Adwords Item Condition',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                            'default' => 'new',
                            'option' => ['values' => [
                                '' => '--select condition--',
                                'new' => 'new',
                                'refurbished' => 'refurbished',
                                'used' => 'used'
                            ]]
                        ]
                    );
                }
            } catch (\Exception $exception) {
            } catch (\Error $error) {
            }
        }
        if (version_compare($context->getVersion(), '1.0.4', '<')) {
            try {
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                $groupName = 'Adwords';
                $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
                $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
                $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
                $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_status')) {
//                    $eavSetup->removeAttribute(\Magento\Catalog\Model\Product::ENTITY, 'adwords_tp_status');
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_status',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Status',
                            'input' => 'boolean',
                            'type' => 'int',
                            'label' => 'Adwords Status',
                            'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'default' => 1,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL
                        ]
                    );
                }
            } catch (\Exception $exception) {
            } catch (\Error $error) {
            }
        }
        if (version_compare($context->getVersion(), '1.0.5', '<')) {
            try {
                $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
                $groupName = 'Adwords';
                $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
                $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
                $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
                $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);
                if (!$this->eavAttribute->getIdByCode(
                    'catalog_product', 'adwords_tp_buffer_quantity')) {
                    $eavSetup->addAttribute(
                        'catalog_product',
                        'adwords_tp_buffer_quantity',
                        [
                            'group' => 'Adwords',
                            'note' => 'Adwords Buffer Quantity',
                            'input' => 'text',
                            'type' => 'varchar',
                            'label' => 'Adwords Buffer Quantity',
                            'backend' => '',
                            'visible' => 1,
                            'required' => 0,
                            'sort_order' => 3,
                            'user_defined' => 1,
                            'comparable' => 0,
                            'visible_on_front' => 0,
                            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                            'frontend_class' => 'validate-length maximum-length-150',
                        ]
                    );
                }
            } catch (\Exception $exception) {
            } catch (\Error $error) {
            }
        }
    }
}
