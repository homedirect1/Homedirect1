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


namespace Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Import\Edit;

/**
 * Class Tabs
 * @package Ced\CsHyperlocal\Block\Adminhtml\Import\Edit
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {

        parent::_construct();
        $this->setId('import_tabs');
        $this->setDestElementId('import_form');
        $this->setTitle(__('Zipcodes CSV'));
    }
    protected function _beforeToHtml()
    {
        $this->addTab(
            'main',
            [
                'label' => __('Zipcodes CSV'),
                'title' => __('Zipcodes CSV'),
                'content' => $this->getLayout()->createBlock('Ced\CsHyperlocal\Block\Adminhtml\Zipcode\Import\Edit\Tab\General')->toHtml(),
                'active' => true
            ]
        );

        return parent::_beforeToHtml();
    }
}

