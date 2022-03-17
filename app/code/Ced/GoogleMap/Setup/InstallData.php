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

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Model\Entity\Attribute\SetFactory;
use Magento\Eav\Model\ResourceModel\Entity\Attribute;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    /**
     * @var CustomerSetupFactory
     */
    private $customerSetupFactory;

    /**
     * @var SetFactory
     */
    private $attributeSetFactory;

    private $attributeResource;

    public function __construct(
        Attribute $attributeResource,
        SetFactory $attributeSetFactory,
        CustomerSetupFactory $customerSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->attributeSetFactory = $attributeSetFactory;
        $this->attributeResource = $attributeResource;
    }


    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->addLocationAttribute($setup);
        $setup->endSetup();
    }

    public function addLocationAttribute(ModuleDataSetupInterface $setup)
    {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $eav = $customerSetup->getEavConfig();
        $entityTypeId = AddressMetadataInterface::ENTITY_TYPE_ADDRESS;
        $customerAddressEntity = $eav->getEntityType($entityTypeId);
        $attributeSetId = $customerAddressEntity->getDefaultAttributeSetId();
        $attributeSet = $this->attributeSetFactory->create();
        $attributeGroupId = $attributeSet->getDefaultGroupId($attributeSetId);

        $codes = ['latitude', 'longitude'];
        foreach ($codes as $code) {
            if (!$customerSetup->getAttributeId($entityTypeId, $code)) {
                $customerSetup->addAttribute(
                    $entityTypeId,
                    $code,
                    [
                        'type' => 'varchar',
                        'label' => ucfirst($code),
                        'input' => 'text',
                        'required' => false,
                        'visible' => true,
                        'user_defined' => true,
                        'position' => 10,
                        'system' => 0,
                    ]
                );

                $attribute = $eav->getAttribute($entityTypeId, $code);
                $attribute->addData([
                    'attribute_set_id' => $attributeSetId,
                    'attribute_group_id' => $attributeGroupId,
                    'used_in_forms' => [
                        'adminhtml_customer_address',
                        'customer_address_edit',
                        'customer_register_address'
                    ],
                ]);
                $attribute->save();

                //$this->attributeResource->save($attribute);
            }
        }
    }
}
