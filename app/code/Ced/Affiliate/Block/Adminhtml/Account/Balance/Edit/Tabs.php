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
 * @package     Ced_Affiliate
 * @author      CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license     http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\Affiliate\Block\Adminhtml\Account\Balance\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('balance_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Amount Information'));
    }

    /**
     * @return $this
     */
    protected function _beforeToHtml()
    {
    	
       $this->addTab(
            'balance_tab',
            [
                'label' => __('Payment Information'),
                'title' => __('Payment Information'),
                'content' => $this->getLayout()->createBlock('Ced\Affiliate\Block\Adminhtml\Account\Balance\Edit\Tab\Main')->toHtml(),
                'active' => true
            ]
        );
        return parent::_beforeToHtml();
    }
}
