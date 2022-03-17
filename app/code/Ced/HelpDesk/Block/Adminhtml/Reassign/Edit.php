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
 * @package     Ced_HelpDesk
 * @author 		CedCommerce Core Team <connect@cedcommerce.com>
 * @copyright   Copyright CedCommerce (http://cedcommerce.com/)
 * @license      http://cedcommerce.com/license-agreement.txt
 */
namespace Ced\HelpDesk\Block\Adminhtml\Reassign;
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /*
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     * */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }
 
    protected function _construct()
    {  
        $this->_objectId = 'id';
        $this->_blockGroup = 'Ced_HelpDesk';
        $this->_controller = 'adminhtml_reassign';
        parent::_construct();
        
     
        $this->buttonList->update('save', 'label', __('Assign'));
        
        $this->removeButton('delete');
        $this->removeButton('saveandcontinue');
    }
    /*
     * get Header text
     * @return string
     * */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('ced_ticket_assign')->getId()) {
            return __("Edit Status '%1'", $this->escapeHtml($this->_coreRegistry->registry('ced_ticket_assign')->getTitle()));
        } else {
            return __('New Status');
        }
    }
    /*
     * @param $resourceId
     * @return boolean
     * */

    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
    /*
     * change back url
     * @return string
     * */
    public function getBackUrl()
    {   $data = $this->_coreRegistry->registry('ced_ticket_assign');
        return $this->getUrl('*/tickets/ticketsinfo');
    }
}