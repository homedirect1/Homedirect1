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
namespace Webkul\Recurring\Block\Adminhtml\Duration\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('duration_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Duration Information'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $block = \Webkul\Recurring\Block\Adminhtml\Duration\Edit\Tab\Duration::class;
        $this->addTab(
            'durations',
            [
                'label' => __('Information'),
                'content' => $this->getLayout()->createBlock($block, 'duration')->toHtml()
            ]
        );
        return parent::_prepareLayout();
    }
}
