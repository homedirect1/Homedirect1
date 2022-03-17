<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Recurring
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Recurring\Block\Adminhtml\Plans\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('plans_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Subscription Type Information'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $block   = \Webkul\Recurring\Block\Adminhtml\Plans\Edit\Tab\Plan::class;
        $productsBlock = \Webkul\Recurring\Block\Adminhtml\Plans\Edit\Tab\Products::class;
        $this->addTab(
            'plan',
            [
                'label' => __('Information'),
                'content' => $this->getLayout()->createBlock($block, 'plan')->toHtml(),
            ]
        );
        
        return parent::_prepareLayout();
    }
}
