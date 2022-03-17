<?php

namespace Ced\GShop\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        $installer = $setup;
        $installer->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            $tableName = $setup->getTable('gxpress_accounts');
            if ($setup->getConnection()->isTableExists($tableName)) {
                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'content_language',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 10,
                            'nullable' => true,
                            'comment' => 'Content Language'
                        ]
                    );
                $installer->getConnection()->addColumn(
                    $tableName,
                    'target_country',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 10,
                        'nullable' => true,
                        'comment' => 'Target Country'
                    ]
                );
                $installer->getConnection()->addColumn(
                    $tableName,
                    'included_destination',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 50,
                        'nullable' => true,
                        'comment' => 'Included Destination'
                    ]
                );
            }
            $installer->endSetup();
        }
        if (version_compare($context->getVersion(), "1.0.5", "<")) {
            $tableName = $setup->getTable('gxpress_accounts');
            if ($setup->getConnection()->isTableExists($tableName)) {
                $installer->getConnection()
                    ->addColumn(
                        $tableName,
                        'account_type',
                        [
                            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                            'length' => 10,
                            'nullable' => true,
                            'comment' => 'Account Type'
                        ]
                    );
            }
        }
    }
}
