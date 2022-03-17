<?php


namespace Ced\Affiliate\Setup;


use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{

    /**
     * Upgrades data for a module
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if ( version_compare($context->getVersion(), '0.0.5') < 0
        ) {
            $this->updateEarnedAmountInReferralTransaction($setup);
        }

        $setup->endSetup();
    }

    protected function updateEarnedAmountInReferralTransaction(ModuleDataSetupInterface $installer) {
        $tableName = $installer->getTable('ced_affiliatereferral_transaction');
        $couponTableName = $installer->getTable('ced_affiliatediscount_coupons');
        $connection = $installer->getConnection();
        if ($connection->isTableExists($tableName) && $connection->tableColumnExists($tableName, 'earned_amount')) {
            $query = "UPDATE {$tableName} t set t.earned_amount =  -(select c.amount from {$couponTableName} c 
                        where t.description like concat('%: ',c.coupon_code,'%') and c.customer_id = t.customer_id)
                        where t.transaction_type = 2 and t.earned_amount is not null";
            $connection->query($query);
        }
    }
}