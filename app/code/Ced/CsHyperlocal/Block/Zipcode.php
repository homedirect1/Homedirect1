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

namespace Ced\CsHyperlocal\Block;

class Zipcode extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'zipcode';
        $this->_blockGroup = 'Ced_CsHyperlocal';
        $this->_headerText = __('Manage Zipcodes');
        $this->_addButtonLabel = __('Add Zipcode');
        parent::_construct();
        $this->setData('area','adminhtml');
    }

    protected function _addNewButton()
    {
        $locationId = $this->getRequest()->getParam('id');
        $newurl=$this->getUrl('*/zipcode/newzipcode',array('location_id'=>$locationId));
        $backtoUrl = $this->getUrl('*/shiparea/index');
        $importurl = $this->getUrl('*/zipcode/import',array('location_id'=>$locationId));
        $this->addButton(
            'back',
            [
                'label' => __('Back'),
                'onclick' => "setLocation('{$backtoUrl}')",
                'class' => 'back',
                'area' => 'adminhtml'
            ]
        );
        $this->addButton(
            'add',
            [
                'label' => __('Add Zipcode'),
                'onclick' => "setLocation('{$newurl}')",
                'class' => 'add primary',
                'area' => 'adminhtml'
            ]
        );
        $this->addButton(
            'import_csv',
            [
                'label' => __('Import CSV'),
                'onclick' => "setLocation('{$importurl}')",
                'class' => 'add primary',
                'area' => 'adminhtml'
            ]
        );
    }
}
