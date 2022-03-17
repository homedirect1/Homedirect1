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
 * @package     Ced_Walmart
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Integrator\Setup;

use Ced\Integrator\Model\Constants;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Sales\Setup\SalesSetup;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Quote\Setup\QuoteSetup;

class UpgradeData implements  UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    private $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    private $salesSetupFactory;

    public function __construct(
        SalesSetupFactory $salesSetupFactory,
        QuoteSetupFactory $quoteSetupFactory
    )
    {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    public function upgrade (
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        if (version_compare($context->getVersion(), '1.1.0') < 0) {
            $setup->startSetup();
            /** @var QuoteSetup $quoteSetup */
            $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
            /** @var SalesSetup $salesSetup */
            $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);

            $attributeOptions = [
                'visible'  => true,
                'required' => false,
                'default' => 'Not Applicable'
            ];
            $quoteSetup->addAttribute('quote', Constants::ORDER_ATTRIBUTE, $attributeOptions);
            $salesSetup->addAttribute('order', Constants::ORDER_ATTRIBUTE, $attributeOptions);
            $setup->endSetup();
        }
    }
}
