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
 * @package     Ced_CsHyperlocal
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsHyperlocal\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Ced\CsMarketplace\Setup\CsMarketplaceSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{

    const ENTITY_TYPE = \Magento\Catalog\Model\Product::ENTITY;

    /**
     * CsMarketplace setup factory
     *
     * @var CsMarketplaceSetupFactory
     */
    private $csmarketplaceSetupFactory;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var \Ced\CsMarketplace\Model\Vendor\FormFactory
     */
    protected $formFactory;

    /**
     * InstallData constructor.
     * @param CsMarketplaceSetupFactory $csmarketplaceSetupFactory
     * @param EavSetupFactory $eavSetupFactory
     * @param \Ced\CsMarketplace\Model\Vendor\FormFactory $formFactory
     */
    public function __construct(
        CsMarketplaceSetupFactory $csmarketplaceSetupFactory,
        EavSetupFactory $eavSetupFactory,
        \Ced\CsMarketplace\Model\Vendor\FormFactory $formFactory
    )
    {
        $this->csmarketplaceSetupFactory = $csmarketplaceSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /**
         * @var CsMarketplaceSetup $csmarketplaceSetup
         */
         $csmarketplaceSetup = $this->csmarketplaceSetupFactory->create(['setup' => $setup]);
         $setup->startSetup();
         $csmarketplaceSetup->installEntities();
         $csmarketplaceSetup->addAttribute('csmarketplace_vendor', 'location', array(
             'group'			=> 'General Information',
             'visible'      	=> true,
             'position'      => 10,
             'type'          => 'static',
             'label'         => 'Location',
             'input'         => 'text',
             'required'      => true,
             'user_defined'  => false,
         ));
         $csmarketplaceSetup->addAttribute('csmarketplace_vendor', 'latitude', array(
             'group'			=> 'General Information',
             'visible'      	=> true,
             'position'      => 20,
             'type'          => 'static',
             'label'         => 'Latitude',
             'input'         => 'text',
             'required'      => true,
             'user_defined'  => false,
         ));
         $csmarketplaceSetup->addAttribute('csmarketplace_vendor', 'longitude', array(
             'group'			=> 'General Information',
             'visible'      	=> true,
             'position'      => 30,
             'type'          => 'static',
             'label'         => 'Longitude',
             'input'         => 'text',
             'required'      => true,
             'user_defined'  => false,

         ));

         $vendorAttributes = $this->formFactory->create()->getCollection();

         foreach($vendorAttributes as $vendorAttribute) {
             $vendorMainAttribute = $this->formFactory->create()->load($vendorAttribute->getId());

             if($vendorMainAttribute->getAttributeCode()=='location'){
                 $vendorMainAttribute->setData('use_in_registration',1);
                 $vendorMainAttribute->setData('is_visible',1);
                 $vendorMainAttribute->setData('use_in_left_profile',0);
                 $vendorMainAttribute->save();
             }
             if($vendorMainAttribute->getAttributeCode()=='latitude'){

                 $vendorMainAttribute->setData('use_in_registration',1);
                 $vendorMainAttribute->setData('is_visible',1);
                 $vendorMainAttribute->setData('use_in_left_profile',0);
                 $vendorMainAttribute->save();
             }
             if($vendorMainAttribute->getAttributeCode()=='longitude'){

                 $vendorMainAttribute->setData('use_in_registration',1);
                 $vendorMainAttribute->setData('is_visible',1);
                 $vendorMainAttribute->setData('use_in_left_profile',0);
                 $vendorMainAttribute->save();
             }
         }

        /** create product attribute location */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute('catalog_product', 'shipping_product_location', [
                'group' => '',
                'note' => '',
                'input' => 'multiselect',
                'type' => 'text',
                'label' => 'Shipping Location',
                'backend' => 'Magento\Eav\Model\Entity\Attribute\Backend\ArrayBackend',
                'required' => false,
                'sort_order' => 200,
                'user_defined' => 1,
                'source' => 'Ced\CsHyperlocal\Model\Product\Location',
                'comparable' => 0,
                'visible' => true,
                'visible_on_front' => 0,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
            ]
        );
        $entityTypeId = $eavSetup->getEntityTypeId(self::ENTITY_TYPE);
        $defaultId = $eavSetup->getDefaultAttributeSetId(self::ENTITY_TYPE);
        $eavSetup->addAttributeToSet($entityTypeId, $defaultId, 'General', 'shipping_product_location');
        $setup->endSetup();

    }

}
