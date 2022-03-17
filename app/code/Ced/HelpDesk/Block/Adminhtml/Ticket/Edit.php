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
namespace Ced\HelpDesk\Block\Adminhtml\Ticket;
 
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /*
     * @var $_coreRegistry
     * */
    protected $_coreRegistry = null;
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
        $this->_coreRegistry = $registry->registry('ced_ticket');
        parent::__construct($context, $data);
    }
 
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Ced_HelpDesk';
        $this->_controller = 'adminhtml_ticket';
 
        parent::_construct();
 
        $this->buttonList->update('save', 'label', __('Save Ticket'));
        $this->buttonList->add(
            'saveandcontinue',
            [
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => [
                    'mage-init' => [
                        'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                    ],
                ]
            ],
            -100
        );
        $this->buttonList->update('delete', 'label', __('Delete'));
    }
    /*
     * get header text
     * @return string
     * */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->getId()) {
            return __("Edit Post '%1'", $this->escapeHtml($this->_coreRegistry->getTitle()));
        } else {
            return __('New Agent');
        }
    }
    /*
     * change save and continue url
     * @return string
     * */
    protected function _getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']);
    }
    /*
    * change back url
    * @return string
    * */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/ticketsinfo');
    }
    /*
    * change delete url
    * @return string
    * */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [$this->_objectId => $this->getRequest()->getParam($this->_objectId)]);
    }
}