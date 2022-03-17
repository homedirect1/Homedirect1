<?php

namespace Ced\CsPromotions\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
	/**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
    	$installer = $setup;
        $installer->startSetup();
        $connection = $setup->getConnection();
        $column = [
            'type' => Table::TYPE_SMALLINT,
            'length' => null,
            'nullable' => false,
            'comment' => 'is_approve',
            'default' => '0'
        ];
        
        if (version_compare($context->getVersion(), '1.0.1', '<')) {
        	$connection->addColumn($setup->getTable('catalogrule'), 'is_approve', $column);
        	$connection->addColumn($setup->getTable('salesrule'), 'is_approve', $column);
        }
    }
}