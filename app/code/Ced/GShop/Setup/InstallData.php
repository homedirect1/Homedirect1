<?php
/**
 * CedCommerce
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End User License Agreement(EULA)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://cedcommerce.com/license-agreement.txt
 *
 * @category  Ced
  * @package  Ced_GShop
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CEDCOMMERCE(http://cedcommerce.com/)
 * @license   http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Setup;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

if (defined('DS') === false) {
    define('DS', DIRECTORY_SEPARATOR);
}

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var EavSetupFactory
     */
    public $eavSetupFactory;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    public $objectManager;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    public $eavAttribute;

    /**
     * InstallData constructor.
     * @param EavSetupFactory $eavSetupFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\App\State $state
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $eavAttribute,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\State $state
    ) {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->objectManager = $objectManager;
        $this->eavAttribute = $eavAttribute;
        $this->directoryList = $directoryList;
        //if(!$state->getAreaCode()) {
        try {
            $state->setAreaCode('frontend');
        } catch (\Exception $e) {
        }

        //}
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $appPath = $this->directoryList->getRoot();

        $data = $objectManager->create('\Ced\GShop\Helper\Data');
        $gxpressPath = $appPath . DS . "app" . DS . "code" . DS . "Ced" . DS . "GShop" . DS . "Setup" . DS . "GXpressJson" . DS;

        $path = $gxpressPath . "gxpress_category.json";
        $categories = $data->loadFile($path);

        $path = $gxpressPath . "gxpress_attribute.json";
        $attributes = $data->loadFile($path);

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

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        /**
         * Add attributes to the eav/attribute
         */

        $groupName = 'Google';
        $entityTypeId = $eavSetup->getEntityTypeId(\Magento\Catalog\Model\Product::ENTITY);
        $attributeSetId = $eavSetup->getDefaultAttributeSetId($entityTypeId);
        $eavSetup->addAttributeGroup($entityTypeId, $attributeSetId, $groupName, 1000);
        $eavSetup->getAttributeGroupId($entityTypeId, $attributeSetId, $groupName);

        if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_profile_id')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'google_profile_id',
                [
                    'group' => 'Google',
                    'note' => 'Google Profile Id ',
                    'input' => 'text',
                    'type' => 'varchar',
                    'label' => 'Google Profile Id',
                    'backend' => '',
                    'visible' => 0,
                    'required' => 0,
                    'sort_order' => 5,
                    'user_defined' => 0,
                    'comparable' => 0,
                    'visible_on_front' => 0,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_configurable' => false,
                ]
            );
        }

        if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_condition')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'google_condition',
                [
                    'group' => 'Google',
                    'note' => 'Google Condition',
                    'input' => 'select',
                    'type' => 'varchar',
                    'label' => 'Google Condition',
                    'backend' => '',
                    'visible' => 1,
                    'required' => 0,
                    'sort_order' => 5,
                    'user_defined' => 1,
                    'source' => 'Ced\GShop\Model\Source\Productcondition',
                    'comparable' => 0,
                    'visible_on_front' => 0,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                    'is_configurable' => false
                ]
            );
        }

        if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_product_status')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'google_product_status',
                [
                    'group' => 'Google',
                    'note' => 'Google Status',
                    'input' => 'text',
                    'type' => 'text',
                    'label' => 'Google Status',
                    'backend' => '',
                    'visible' => 1,
                    'required' => 0,
                    'sort_order' => 5,
                    'user_defined' => 1,
                    'source' => 'Ced\GShop\Model\Source\Productstatus',
                    'comparable' => 0,
                    'visible_on_front' => 0,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                ]
            );
        }

        if (!$this->eavAttribute->getIdByCode('catalog_product', 'google_product_validation')) {
            $eavSetup->addAttribute(
                'catalog_product',
                'google_product_validation',
                [
                    'group' => 'Google',
                    'note' => 'Google Validation',
                    'input' => 'hidden',
                    'type' => 'text',
                    'label' => 'Google Validation',
                    'backend' => '',
                    'visible' => 1,
                    'required' => 0,
                    'sort_order' => 5,
                    'user_defined' => 1,
                    'comparable' => 0,
                    'visible_on_front' => 0,
                    'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
                ]
            );
        }
    }
}
