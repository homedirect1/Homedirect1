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
 * @package     Ced_CsAdvTransaction
 * @author     CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright   Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsAdvTransaction\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Ced\CsAdvTransaction\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var \Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * @var \Magento\Config\Model\ResourceModel\Config
     */
    protected $config;

    /**
     * UpgradeSchema constructor.
     * @param \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     * @param \Magento\Config\Model\ResourceModel\Config $config
     */
    public function __construct(
        \Ced\CsAdvTransaction\Model\FeeFactory $feeFactory,
        \Magento\Config\Model\ResourceModel\Config $config
    )
    {
        $this->feeFactory = $feeFactory;
        $this->config = $config;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), "1.0.0", "<")) {
            $installer = $setup;
            $installer->startSetup();
            $tableName = $installer->getTable('ced_csmarketplace_vendor_sales_order');
            if ($installer->getConnection()->isTableExists($tableName) == true) {
                $connection = $setup->getConnection();
                $connection
                    ->addColumn(
                        $tableName,
                        'vendor_earn',
                        \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        null,
                        array('nullable' => false),
                        'vendor_earn'
                    );
            }
            $installer->getConnection()->modifyColumn(
                $installer->getTable('ced_csmarketplace_vendor_payments'),
                'amount_desc',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'length' => 25555],
                'amount_desc Method'
            );
            $installer->endSetup();
            $installer = $setup;

            $installer->startSetup();

            $table = $installer->getConnection()->newTable(
                $installer->getTable('ced_csadvtransaction_payment_request')
            )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                array('identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true,),
                'id'
            )
                ->addColumn(
                    'vendor_id', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(), 'vendor_id'
                )->addColumn(
                    'order_ids', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, array(), 'order_ids'
                )->addColumn(
                    'created_at', \Magento\Framework\DB\Ddl\Table::TYPE_DATE, null, array(), 'created_at'
                )
                ->addColumn(
                    'status', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 20, array(), 'status'
                )
                ->addColumn(
                    'amount', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 2055, array(), 'amount'
                );

            $installer->getConnection()->createTable($table);
            $installer->endSetup();


            $feeModel = $this->feeFactory->create();
            $feeModel->setData('field_code', 'fixed_fee');
            $feeModel->setData('field_label', 'Fixed Fee');
            $feeModel->setData('value', 10);
            $feeModel->setData('mode', '');
            $feeModel->setData('order_state', 1);
            $feeModel->setData('is_system', 1);
            $feeModel->setData('type', 'fixed');
            $feeModel->setData('status', 1);
            $feeModel->save();

            $feeModel = $this->feeFactory->create();
            $feeModel->setData('field_code', 'collection_fee');
            $feeModel->setData('field_label', 'Collection Fee');
            $feeModel->setData('value', 10);
            $feeModel->setData('mode', '');
            $feeModel->setData('order_state', 1);
            $feeModel->setData('is_system', 1);
            $feeModel->setData('type', 'fixed');
            $feeModel->setData('status', 1);
            $feeModel->save();


            $value = 'a:1:{s:18:"_1492415709739_739";a:3:{s:3:"tax";s:11:"Service Tax";s:6:"enable";s:1:"1";s:6:"amount";s:1:"1";}}';

            $this->config->saveConfig('ced_csmarketplace/vadvtransaction/vendor_taxes', $value, 'default', 0);
        }
    }
}
