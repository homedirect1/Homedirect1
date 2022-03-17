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

namespace Ced\CsStorePickup\Block;

use Magento\Backend\Block\Widget\Container;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class StorePickup
 * @package Ced\CsStorePickup\Block
 */
class StorePickup extends Container
{
    /**
     * @var string
     */
    protected $_template = 'stores.phtml';

    protected function _construct()
    {
        $this->_controller = 'storepickup';
        $this->_blockGroup = 'Ced_CsStorePickup';
        $this->_headerText = __('Stores');
        parent::_construct();
    }

    /**
     * @return Container
     * @throws LocalizedException
     */
    protected function _prepareLayout()
    {
        $addButtonProps = [
            'id' => 'add_new_rule',
            'label' => __('Add New Store'),
            'class' => 'add primary',
            'onclick' => "location.href='" . $this->getUrl('*/*/edit') . "'",
            'button_class' => '',
        ];
        $this->buttonList->add('add_new', $addButtonProps);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Ced\CsStorePickup\Block\Stores\Grid',
                'ced.csstorepickup.vendor.store.grid'
            )
        );

        return parent::_prepareLayout();

    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddStoreButtonOptions()
    {
        $splitButtonOptions = [];

        $splitButtonOptions = [
            'label' => __('Add Store'),
            'href' => $this->_getStoreCreateUrl()
        ];

        return $splitButtonOptions;
    }


    /**
     * @return string
     */
    protected function _getStoreCreateUrl()
    {

        return $this->getUrl(
            'csstorepickup/*/new'
        );
    }

    /**
     * @return array
     */
    protected function _getAddButtonOptions()
    {

        $splitButtonOptions[] = [
            'label' => __('Add New'),
            'onclick' => "setLocation('" . $this->_getCreateUrl() . "')",
            'area' => 'adminhtml'
        ];

        return $splitButtonOptions;
    }

    /**
     * @return string
     */
    protected function _getCreateUrl()
    {
        return $this->getUrl(
            '*/*/new'
        );
    }

    /**
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}