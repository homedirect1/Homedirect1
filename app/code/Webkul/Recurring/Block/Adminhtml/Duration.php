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
namespace Webkul\Recurring\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

/**
 * Adminhtml Recurring Block Plans
 */
class Duration extends Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_duration';
        $this->_blockGroup = 'Webkul_Recurring';
        $this->_headerText = __('Manage Duration Type');
        parent::_construct();
        $this->buttonList->update('add', 'label', __('Add New Duration'));
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
