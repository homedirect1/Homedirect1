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

namespace Ced\CsDeliveryDate\Model\Vsettings;

use Magento\Framework\Api\AttributeValueFactory;

/**
 * Class Address
 * @package Ced\CsDeliveryDate\Model\Vsettings
 */
class Address extends \Ced\CsMarketplace\Model\FlatAbstractModel
{
    /**
     * @var string
     */
    protected $_code = 'csdeliverydate';

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
     * @var DeliveryweeksFactory
     */
    protected $deliveryweeksFactory;

    /**
     * @var \Ced\CsDeliveryDate\Block\Vsettings\Timestamp
     */
    protected $timestamp;

    /**
     * Address constructor.
     * @param \Ced\CsMarketplace\Helper\Data $marketplaceHelper
     * @param DeliveryweeksFactory $deliveryweeksFactory
     * @param \Ced\CsDeliveryDate\Block\Vsettings\Timestamp $timestamp
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
        \Ced\CsDeliveryDate\Model\Vsettings\DeliveryweeksFactory $deliveryweeksFactory,
        \Ced\CsDeliveryDate\Block\Vsettings\Timestamp $timestamp,
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
        $this->deliveryweeksFactory = $deliveryweeksFactory;
        $this->timestamp = $timestamp;
    }


    /**
     * @return mixed
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
     * @return mixed
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
        $this->_fields['enablesettings'] = array('type' => 'select', 'required' => false, 'values' => array(
            array('label' => __('Yes'), 'value' => 1),
            array('label' => __('No'), 'value' => 0)
        ));
        $this->_fields['ddforguest'] = array('type' => 'select', 'required' => false, 'values' => array(
            array('label' => __('Yes'), 'value' => 1),
            array('label' => __('No'), 'value' => 0)
        ));

        $this->_fields['sameDayDelivery'] = array('type' => 'select', 'required' => false, 'values' => array(
            array('label' => __('Yes'), 'value' => 1),
            array('label' => __('No'), 'value' => 0)
        ));

        $this->_fields['vddnoteforcalander'] = array('type' => 'textarea', 'required' => false);

        $this->_fields['maxdays'] = array('type' => 'text', 'required' => false);

        $this->_fields['alldaydelivery'] = array('type' => 'select', 'required' => false, 'values' => array(
            array('label' => __('Yes'), 'value' => 1),
            array('label' => __('No'), 'value' => 0)
        ));

        $this->_fields['weekdays'] = array('type' => 'multiselect', 'required' => false, 'values' =>
            $this->deliveryweeksFactory->create()->toOptionArray());


        $this->_fields['enablecommentonfrontend'] = array('type' => 'select', 'required' => false, 'values' => array(
            array('label' => __('Yes'), 'value' => 1),
            array('label' => __('No'), 'value' => 0)
        ));

        $this->_fields['commentfieldnote'] = array('type' => 'textarea', 'required' => false);

        $block_obj = $this->timestamp;
        $this->_fields['timestamp'] = array('type' => 'text', 'class' => 'hide',
            'after_element_html' => $block_obj->toHtml());

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
            case 'label'  :
                return __('');
                break;
            case 'sameDayDelivery'  :
                return __('Enable Current Day on Calendar : ');
                break;
            case 'timestamperror'  :
                return __('Currently Timestamp Setting is not availiable : ');
                break;
            case 'ddforguest'  :
                return __('Enable Delivery date for guest users : ');
                break;
            case 'datetimeconfigurationlabel'  :
                return __('Delivery Date and Time Configuration : ');
                break;
            case 'enablesettings' :
                return __('Enable Disable DeliveryDate Settings :');
                break;
            case 'vddnoteforcalander' :
                return __('DeliveryDate Note For Calander : ');
                break;
            case 'maxdays' :
                return __('Choose Maximum Days To Display : ');
                break;
            case 'enablecommentonfrontend' :
                return __('Enable Comment On Frontend : ');
                break;
            case 'commentfieldnote' :
                return __('Field Note(For Comment) : ');
                break;
            case 'postcode' :
                return __('Zip/Postal Code : ');
                break;
            case 'timestamp' :
                return __('Add Time Stamp : ');
                break;
            case 'alldaydelivery' :
                return __('7 Day Delivery : ');
                break;
            case 'weekdays' :
                return __('Select Working Delivery Days :');
                break;
            default :
                return __($key);
                break;
        }
    }
}
