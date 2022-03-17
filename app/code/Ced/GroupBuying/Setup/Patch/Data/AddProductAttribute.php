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
 * @package     Ced_GroupBuying
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */

namespace Ced\GroupBuying\Setup\Patch\Data;

use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Zend_Validate_Exception;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;

class AddProductAttribute implements DataPatchInterface {

    private $moduleDataSetup;
    private $eavSetupFactory;

    /**
     * TODO
     *
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param EavSetupFactory $eavSetupFactory
     */

    public function __construct(
            ModuleDataSetupInterface $moduleDataSetup,
            EavSetupFactory $eavSetupFactory) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    /**
     * TODO
     *
     * @throws Zend_Validate_Exception
     * @throws LocalizedException
     */
    public function apply() {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $eavSetup->addAttribute('catalog_product', 'group_buy', [
            'input' => 'select',
            'source' => Boolean::class,
            'type' => 'int',
            'label' => 'Group Buy Enable',
            'backend' => '',
            'default' => 0,
            'visible' => 1,
            'required' => true,
            'group' => 'Group-Buying',
            'global' => ScopedAttributeInterface::SCOPE_GLOBAL,
        ]);
    }

    /**
     * TODO
     *
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * TODO
     *
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

}
