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
 * @category  Ced
 * @package   Ced_CsDeliveryDate
 * @author    CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright Copyright CedCommerce (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */


namespace Ced\CsDeliveryDate\Model\Vsettings\Methods;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class AbstractModel
 * @package Ced\CsDeliveryDate\Model\Vsettings\Methods
 */
class AbstractModel extends \Ced\CsMarketplace\Model\FlatAbstractModel
{

    const SHIPPING_SECTION = 'csdeliverydate';

    /**
     * @var string
     */
    protected $_code = '';

    /**
     * @var array
     */
    protected $_fields = array();

    /**
     * @var string
     */
    protected $_codeSeparator = '-';

    /**
     * @var \Ced\CsMarketplace\Helper\Data
     */
    protected $marketplaceHelper;

    /**
     * AbstractModel constructor.
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Ced\CsMarketplace\Helper\Data $marketplaceHelper,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        AttributeValueFactory $customAttributeFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    )
    {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );
        $this->marketplaceHelper = $marketplaceHelper;
    }

    /**
     * @return \Ced\CsMarketplace\Helper\Mage_Core_Model_Store|\Magento\Store\Api\Data\StoreInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStore()
    {
        $storeId = (int)$this->getRequest()->getParam('store', 0);
        if ($storeId) {
            return $this->marketplaceHelper->getStore($storeId);
        } else {
            return $this->marketplaceHelper->getStore();
        }
    }

    /**
     * Get current store
     *
     * @return Mage_Core_Model_Store
     */

    public function getStoreId()
    {
        return $this->getStore()->getId();
    }


    /**
     * Get the code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * Get the code separator
     *
     * @return string
     */
    public function getCodeSeparator()
    {
        return $this->_codeSeparator;
    }

    /**
     *  Retreive input fields
     *
     * @return array
     */
    public function getFields()
    {
        $this->_fields = array();
        $this->_fields['active'] = array('type' => 'select',
            'values' => array(array('label' => __('Yes'), 'value' => 1),
                array('label' => __('No'), 'value' => 0))
        );
        return $this->_fields;
    }

    /**
     * Retreive labels
     *
     * @param string $key
     * @return string
     */
    public function getLabel($key)
    {
        switch ($key) {
            case 'active' :
                return __('Active');
                break;
            default :
                return __($key);
                break;
        }
    }

    /**
     * @param $methodData
     * @return bool
     */
    public function validateSpecificMethod($methodData)
    {
        if (count($methodData) > 0) {
            return true;
        } else {
            return false;
        }
    }
}
