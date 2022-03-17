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
 * @package     Ced_GoogleMap
 * @author 	    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license     https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\GoogleMap\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

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
        $setup->startSetup();

        $this->addLocationFieldInQuoteAddress($setup);

        $setup->endSetup();
    }

    public function addLocationFieldInQuoteAddress(SchemaSetupInterface  $setup)
    {
        $connection = $setup->getConnection();

        $tableName = $setup->getTable('quote_address');
        if ($connection->isTableExists($tableName)) {
            if ($connection->tableColumnExists($tableName, 'longitude') === false) {
                $connection->addColumn(
                    $tableName,
                    'longitude',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'comment' => 'Longitude'
                    ]
                );
            }

            if ($connection->tableColumnExists($tableName, 'latitude') === false) {
                $connection->addColumn(
                    $tableName,
                    'latitude',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'comment' => 'Latitude'
                    ]
                );
            }
        }

        $tableName = $setup->getTable('sales_order_address');
        if ($connection->isTableExists($tableName)) {
            if ($connection->tableColumnExists($tableName, 'latitude') === false) {
                $connection->addColumn(
                    $tableName,
                    'latitude',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'comment' => 'Latitude'
                    ]
                );
            }

            if ($connection->tableColumnExists($tableName, 'longitude') === false) {
                $connection->addColumn(
                    $tableName,
                    'longitude',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'nullable' => true,
                        'length' => 255,
                        'comment' => 'Longitude'
                    ]
                );
            }
        }
    }
}
