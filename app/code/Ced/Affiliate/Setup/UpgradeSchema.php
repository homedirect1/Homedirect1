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
 * @category  Ced
 * @package   Ced_Affiliate
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\Affiliate\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $connection = $setup->getConnection();

        if ( version_compare($context->getVersion(), '0.0.2') < 0
        ) {
            $column = [
                'type' => Table::TYPE_DECIMAL,
                'length'    => '10,4',
                'nullable' => false,
                'comment' => 'Custom discount'
            ];

            $connection->addColumn($setup->getTable('sales_order'), 'customdiscount', $column);
        }

        if ( version_compare($context->getVersion(), '0.0.3') < 0
        ) {
            //add import type column in import table
            $this->updateTimestampInWithdrawalRequest($installer);
        }

        if ( version_compare($context->getVersion(), '0.0.4') < 0
        ) {
            //add import type column in import table
            $this->updateEarnedAmountInTransaction($installer);
        }

        $installer->endSetup();
    }

    protected function updateTimestampInWithdrawalRequest(SchemaSetupInterface $installer) {
        $tableName = $installer->getTable('ced_affiliate_withdrawlrequest');
        $connection = $installer->getConnection();
        if ($connection->isTableExists($tableName) == true) {
            if ($connection->tableColumnExists($tableName, 'created_at') === true) {
                $connection->modifyColumn(
                    $tableName,
                    'created_at',
                    [
                        'type' => Table::TYPE_TIMESTAMP,
                        'default' => Table::TIMESTAMP_INIT,
                    ]
                );
            }
        }
    }

    protected function updateEarnedAmountInTransaction(SchemaSetupInterface $installer) {
        $tableName = $installer->getTable('ced_affiliatereferral_transaction');
        $connection = $installer->getConnection();
        if ($connection->isTableExists($tableName) == true) {
            if ($connection->tableColumnExists($tableName, 'earned_amount') === true) {
                $connection->modifyColumn(
                    $tableName,
                    'earned_amount',
                    [
                        'type' => Table::TYPE_DECIMAL,
                        'length' => '10,2',
                        'unsigned' => false
                    ]
                );
            }
        }
    }
}
