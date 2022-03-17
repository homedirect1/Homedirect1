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
 * @package     Ced_CsSla
 * @author        CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsSla\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 * @package Ced\CsSla\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * @var Ced\CsAdvTransaction\Model\FeeFactory
     */
    protected $feeFactory;

    /**
     * UpgradeSchema constructor.
     * @param Ced\CsAdvTransaction\Model\FeeFactory $feeFactory
     */
    public function __construct(\Ced\CsAdvTransaction\Model\FeeFactory $feeFactory)
    {
        $this->feeFactory = $feeFactory;
    }

    /**
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $tableName = $installer->getTable('ced_csmarketplace_vendor_sales_order');
        if ($installer->getConnection()->isTableExists($tableName) == true) {
            $connection = $setup->getConnection();
            $connection
                ->addColumn(
                    $tableName,
                    'order_status',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    array('nullable' => false),
                    'order_status'
                );
        }

        $installer->endSetup();

        $feeModel = $this->feeFactory->create();
        $feeModel->setData('field_code', 'sla_dispatch_fee');
        $feeModel->setData('field_label', 'Dispatch Fee');
        $feeModel->setData('value', 10);
        $feeModel->setData('mode', 'debit');
        $feeModel->setData('order_state', '');
        $feeModel->setData('is_system', 1);
        $feeModel->setData('type', 'fixed');
        $feeModel->setData('status', 1);
        $feeModel->save();

        $feeModel = $this->feeFactory->create();
        $feeModel->setData('field_code', 'sla_cancel_fee');
        $feeModel->setData('field_label', 'Cancellation Fee');
        $feeModel->setData('value', 10);
        $feeModel->setData('mode', 'debit');
        $feeModel->setData('order_state', 3);
        $feeModel->setData('is_system', 1);
        $feeModel->setData('type', 'fixed');
        $feeModel->setData('status', 1);
        $feeModel->save();
    }
}
