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
 * @package     Ced_DeliveryDate
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\DeliveryDate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();

            /**
             * add columns (cedDeliveryDate,cedDeliveryComment) to additional tables-
             * quote
             * quote_item
             * sales_order
             * sales_order_item
             *
             **/
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'cedDeliveryDate',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Date',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'cedDeliveryComment',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Comment',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote'),
                'cedTimestamp',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced Timestamp',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'cedDeliveryDate',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Date',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'cedDeliveryComment',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Comment',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('quote_item'),
                'cedTimestamp',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced cedTimestamp',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'cedDeliveryDate',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Date',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'cedTimestamp',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced cedTimestamp',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order'),
                'cedDeliveryComment',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Comment',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'cedDeliveryDate',
                [
                    'type' => 'datetime',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Date',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'cedDeliveryComment',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced Delivery Comment',
                ]
            );
            $installer->getConnection()->addColumn(
                $installer->getTable('sales_order_item'),
                'cedTimestamp',
                [
                    'type' => 'text',
                    'nullable' => true,
                    'comment' => 'Ced cedTimestamp',
                ]
            );

        $installer->endSetup();

    }
}