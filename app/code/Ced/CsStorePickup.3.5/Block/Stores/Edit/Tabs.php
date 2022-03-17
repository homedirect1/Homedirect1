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
 * @category  Ced
 * @package   Ced_CsStorePickup
 * @author    CedCommerce Core Team <connect@cedcommerce.com >
 * @copyright Copyright CEDCOMMERCE (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\CsStorePickup\Block\Stores\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    public function _construct()
    {

        parent::_construct();
        $this->setId('post_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Vendor Store Pickup Information'));
        $this->setData('area','adminhtml');

    }

    public function _beforeToHtml()
    {
        $this->addTab(
            'CsStore_basic_Information', [
                'label' => __('Store Basic Information'),
                'title' => __('Store Basic Information'),
                'content' => $this->getLayout()->createBlock(
                    'Ced\CsStorePickup\Block\Stores\Edit\Tab\Main'
                )->SetTemplate('ced/storeinfo.phtml')->toHtml(),
                'active' => true
            ]
        );

        $this->addTab(
            'CsStore_Hour_Information', [
                'label'     => __('Store Hour Information '),
                'content'   => $this->getLayout()->createBlock('Ced\CsStorePickup\Block\Stores\Edit\Tab\Storehour')->SetTemplate('ced/storehour.phtml')->toHtml(),
            ]
        );
        return parent::_beforeToHtml();
    }
}
