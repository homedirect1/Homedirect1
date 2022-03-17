<?php

namespace Ced\CsMultiSeller\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
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
        $setup->startSetup();

        $tableName = $setup->getTable('ced_csmarketplace_vendor_products');
        $connection = $setup->getConnection();

        if ($setup->getConnection()->isTableExists($tableName) == true) {
            $connection->addColumn($tableName,
                'is_configurable_child',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Configurable'
            );

            $connection->addColumn($tableName,
                'configurable_product_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'configurable product id'
            );
        }

        if (version_compare($context->getVersion(), '0.0.1') < 0) {
            $connection->addColumn(
            $setup->getTable('ced_csmarketplace_vendor_products'),
            'option_tittle',
            [
                'type' =>  \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                'length' => 255,
                'nullable' => true,
                'default' => '',
                'comment' => 'option tittle'
            ]);
        }

        $setup->endSetup();
    }
}
