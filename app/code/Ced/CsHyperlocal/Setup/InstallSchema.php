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
 * @author 	CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

        /**
         * Create table 'ced_cshyperlocal_shipping_area'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_cshyperlocal_shipping_area')
        )->addColumn('id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn('location',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Shipping Location'
        )->addColumn('latitude',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Latitude'
        )->addColumn('longitude',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Longitude'
        )->addColumn('vendor_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => 0],
            'Vendor Id'
        )->addColumn('city',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'City'
        )->addColumn('state',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'State'
        )->addColumn('country',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Country'
        )->addColumn('zipcode_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Zipcode Type'
        )->addColumn('status',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => 0],
            'Enabled = 1 , Disabled = 0'
        )->setComment('Hyperlocal System');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'ced_cshyperlocal_zipcode'
         */
        $table = $installer->getConnection()->newTable(
            $installer->getTable('ced_cshyperlocal_zipcode')
        )->addColumn('id',
            Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn('location_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => 0],
            'Shipping Location Id'
        )->addColumn('vendor_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'default' => 0],
            'Shipping Vendor Id'
        )->addColumn('zipcode',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'zipcode'
        )->addForeignKey(
            $installer->getFkName(
                'ced_cshyperlocal_zipcode',
                'location_id',
                'ced_cshyperlocal_shipping_area',
                'id'
            ),
            'location_id',
            $installer->getTable('ced_cshyperlocal_shipping_area'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment('Hyperlocal zipcodes');
        $installer->getConnection()->createTable($table);


        /** add column in vendor table */
        $vendorTable = $installer->getTable('ced_csmarketplace_vendor');
        if ($installer->getConnection ()->isTableExists ( $vendorTable ) == true) {
            $columns = [
                'location' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    'nullable' => false,
                    'comment' => 'location',
                ],
                'latitude' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    'nullable' => false,
                    'comment' => 'latitude',
                ],
                'longitude' => [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    255,
                    'nullable' => false,
                    'comment' => 'longitude',
                ],
            ];

            $connection = $installer->getConnection();
            foreach ($columns as $name => $definition) {
                $connection->addColumn($vendorTable, $name, $definition);
            }
        }
        $installer->endSetup();
    }
}
