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
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GShop\Setup;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Ced\GShop\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        $tableName = $installer->getTable('gxpress_profile');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            /**
             * Create table 'gxpress_profile'
             */
            $table = $installer->getConnection()->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )->addColumn(
                    'account_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Account Id'
                )->addColumn(
                    'store_id',
                    Table::TYPE_TEXT,
                    50,
                    [
                        'nullable' => false,
                    ],
                    'Store Id'
                )->addColumn(
                    'profile_code',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false, 'default' => ''],
                    'Profile Code'
                )
                ->addColumn(
                    'profile_status',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => true, 'default' => 1],
                    'Profile Status'
                )
                ->addColumn(
                    'profile_name',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => false, 'default' => ''],
                    'Profile Name'
                )
                ->addColumn(
                    'profile_category',
                    Table::TYPE_TEXT,
                    500,
                    ['nullable' => true, 'default' => ''],
                    'Profile Category'
                )
                ->addColumn(
                    'profile_cat_attribute',
                    Table::TYPE_TEXT,
                    500,
                    ['nullable' => true, 'default' => ''],
                    'Profile Category Attribute'
                )
                ->addColumn(
                    'profile_req_opt_attribute',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Profile Required And Optional Attribute'
                )
                ->addColumn(
                    'profile_cat_feature',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Profile Category Feature'
                )
                ->addColumn(
                    'account_configuration_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Account Configuration'
                )
                ->addIndex(
                    $setup->getIdxName(
                        'gxpress_profile',
                        ['profile_code'],
                        AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    ['profile_code'],
                    ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
                )->setComment('Profile Table')->setOption('type', 'InnoDB')->setOption('charset', 'utf8');
            $installer->getConnection()->createTable($table);
        }

        $tableName = $setup->getTable('gxpress_product_change');
        if ($setup->getConnection()->isTableExists($tableName) != true) {
            /**
             * Create table 'gxpress_product_change'
             */
            $table = $setup->getConnection()->newTable($tableName)
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'product_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['unsigned' => true, 'nullable' => false],
                    'Profile Status'
                )
                ->addColumn(
                    'old_value',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Old Value'
                )
                ->addColumn(
                    'new_value',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'New Value'
                )
                ->addColumn(
                    'action',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Action'
                )
                ->addColumn(
                    'cron_type',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Cron type'
                )
                ->setComment('gxpress Product Change')->setOption('type', 'InnoDB')->setOption('charset', 'utf8');

            $setup->getConnection()->createTable($table);
        }

        if (!$setup->getConnection()->isTableExists($setup->getTable('gxpress_feeds'))) {
            $setup->startSetup();
            $table = $setup->getConnection()->newTable($setup->getTable('gxpress_feeds'))
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true,
                    ],
                    'Id'
                )
                ->addColumn(
                    'feed_type',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => null
                    ],
                    'Feed Type'
                )
                ->addColumn(
                    'feed_source',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => null
                    ],
                    'Feed Source'
                )
                ->addColumn(
                    'feed_date',
                    Table::TYPE_DATETIME,
                    null,
                    [
                        'nullable' => true,
                        'default' => null
                    ],
                    'Feed Date'
                )
                ->addColumn(
                    'feed_file',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                        'default' => null
                    ],
                    'Upload File Path'
                )
                ->addColumn(
                    'feed_errors',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                        'default' => null
                    ],
                    'Feed Errors'
                )
                ->addColumn(
                    'account_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => true],
                    'Account Id'
                );
            $setup->getConnection()->createTable($table);
            $setup->endSetup();
        }

        if (!$setup->getConnection()->isTableExists($setup->getTable('gxpress_accounts'))) {
            $setup->startSetup();
            $table = $setup->getConnection()->newTable($setup->getTable('gxpress_accounts'))
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true,
                    ],
                    'Id'
                )
                ->addColumn(
                    'account_code',
                    Table::TYPE_TEXT,
                    255,
                    ['unique' => true, 'nullable' => false],
                    'Account Code'
                )
                ->addColumn(
                    'merchant_id',
                    Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Account Id'
                )
                ->addColumn(
                    'account_env',
                    Table::TYPE_TEXT,
                    255,
                    ['nullable' => true, 'default' => ''],
                    'Account Environment'
                )
                ->addColumn(
                    'account_store',
                    Table::TYPE_TEXT,
                    50,
                    ['nullable' => true, 'default' => ''],
                    'Account Store'
                )
                ->addColumn(
                    'account_file',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Account File'
                )
                ->addColumn(
                    'account_status',
                    Table::TYPE_BOOLEAN,
                    null,
                    ['nullable' => true],
                    'Account Status'
                )
                ->addColumn(
                    'account_token',
                    Table::TYPE_TEXT,
                    null,
                    ['nullable' => true, 'default' => ''],
                    'Account Token'
                )
                ->addIndex(
                    'account_code',
                    ['account_code'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ]
                )
                ->addIndex(
                    'merchant_id',
                    ['merchant_id'],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ]
                );
            $setup->getConnection()->createTable($table);
            $setup->endSetup();
        }

        // Creating 'gxpress_category_list' table
        if (!$installer->getConnection()->isTableExists($installer->getTable('gxpress_category'))) {
            $setup->startSetup();
            $table = $setup->getConnection()->newTable($setup->getTable('gxpress_category'))
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true,
                    ],
                    'Id'
                )
                ->addColumn(
                    'csv_firstlevel_id',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                    ],
                    'Category first level Id'
                )
                ->addColumn(
                    'csv_secondlevel_id',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                    ],
                    'Category second level Id'
                )
                ->addColumn(
                    'csv_thirdlevel_id',
                    Table::TYPE_TEXT,
                    255,
                    [
                        'nullable' => false,
                    ],
                    'Category third level Id'
                )
                ->addColumn(
                    'csv_fourthlevel_id',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Category fourth level Id'
                )
                ->addColumn(
                    'csv_fifthlevel_id',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Category fifth level Id'
                )
                ->addColumn(
                    'csv_sixthlevel_id',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Category sixth level Id'
                )
                ->addColumn(
                    'csv_seventhlevel_id',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Category seventh level Id'
                )
                ->addColumn(
                    'name',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Name'
                )
                ->addColumn(
                    'path',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Path'
                )
                ->addColumn(
                    'level',
                    Table::TYPE_INTEGER,
                    2,
                    [
                        'nullable' => true,
                    ],
                    'Status'
                )
                ->addColumn(
                    'magento_cat_id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Magento Category Id'
                )
                ->addColumn(
                    'gxpress_required_attributes',
                    Table::TYPE_TEXT,
                    '2M',
                    [
                        'nullable' => true,
                    ],
                    'Required Attributes'
                )
                ->addColumn(
                    'gxpress_attributes',
                    Table::TYPE_TEXT,
                    '2M',
                    [
                        'nullable' => true,
                    ],
                    'Attributes'
                );

            $setup->getConnection()->createTable($table);
            $setup->endSetup();
        }

        // Creating 'gxpress_attributes' table
        if (!$installer->getConnection()->isTableExists($installer->getTable('gxpress_attribute'))) {
            $setup->startSetup();
            $table = $setup->getConnection()->newTable($setup->getTable('gxpress_attribute'))
                ->addColumn(
                    'id',
                    Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true,
                        'auto_increment' => true,
                    ],
                    'Id'
                )
                ->addColumn(
                    'gxpress_attribute_name',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => false,
                    ],
                    'Name'
                )
                ->addColumn(
                    'magento_attribute_code',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Magento Attribute Code'
                )
                ->addColumn(
                    'gxpress_attribute_doc',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Description'
                )
                ->addColumn(
                    'is_mapped',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Is Mapped'
                )
                ->addColumn(
                    'gxpress_attribute_enum',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Enumerations'
                )
                ->addColumn(
                    'gxpress_attribute_level',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Level'
                )
                ->addColumn(
                    'gxpress_attribute_type',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Type'
                )
                ->addColumn(
                    'gxpress_attribute_depends_on',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Depends On'
                )
                ->addColumn(
                    'default_value',
                    Table::TYPE_TEXT,
                    null,
                    [
                        'nullable' => true,
                    ],
                    'Default Value'
                );
            $setup->getConnection()->createTable($table);
            $setup->endSetup();
        }
    }
}
