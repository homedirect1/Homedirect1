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
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (https://cedcommerce.com/)
 * @license      https://cedcommerce.com/license-agreement.txt
 */

namespace Ced\CsStorePickup\Block\Stores\Edit\Tab;

use Ced\StorePickup\Helper\Data;
use Ced\StorePickup\Model\Status;
use Ced\StorePickup\Model\StoreInfo;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Cms\Model\Wysiwyg\Config;
use Magento\Directory\Model\ResourceModel\Country\CollectionFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Store\Model\System\Store;

/**
 * Class Main
 * @package Ced\CsStorePickup\Block\Stores\Edit\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected $_systemStore;

    /**
     * @var Config
     */
    protected $_wysiwygConfig;

    /**
     * @var Status
     */
    protected $_status;

    /**
     * @var CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var Data
     */
    protected $storepickupHelper;

    /**
     * Main constructor.
     * @param Data $storepickupHelper
     * @param Context $context
     * @param CollectionFactory $collectionFactory
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param Config $wysiwygConfig
     * @param Status $status
     * @param StoreInfo $stores
     * @param array $data
     */
    public function __construct(
        Data $storepickupHelper,
        Context $context,
        CollectionFactory $collectionFactory,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        Config $wysiwygConfig,
        Status $status,
        StoreInfo $stores,
        array $data = []
    )
    {
        $this->_systemStore = $systemStore;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_status = $status;
        $this->_stores = $stores;
        $this->_collectionFactory = $collectionFactory;
        $this->storepickupHelper = $storepickupHelper;
        $this->setData('area', 'adminhtml');
        parent::__construct($context, $registry, $formFactory, $data);

    }

    /**
     * @param $pickupid
     * @return mixed
     */
    public function getStores($pickupid)
    {
        $collection = $this->_stores->getCollection()
            ->addFieldToFilter('pickup_id', $pickupid)
            ->getData();
        if (isset($collection)) {
            foreach ($collection as $key => $value) {
                return $value;
            }

        }
    }

    /**
     * @return Phrase|string
     */
    public function getTabLabel()
    {
        return __('Store Pickup Information');
    }


    /**
     * @return Phrase|string
     */
    public function getTabTitle()
    {
        return __('Store Pickup Information');
    }


    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }


    /**
     * @param $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return array
     */
    public function getCountryOptions()
    {
        $options = [];
        foreach ($this->_collectionFactory->create()->loadByStore()->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * @return mixed
     */
    public function getMapKey()
    {
        return $this->storepickupHelper->getStoreConfig('carriers/storepickupshipping/map_apikey');
    }
}
